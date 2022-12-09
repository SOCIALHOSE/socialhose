import {ReduxModule} from '../../../abstract/reduxModule'
import {fromJS} from 'immutable'
import { getItems as getReceiversApi } from '../../../../../api/receiversApi'
import { getHistory as getHistoryApi } from '../../../../../api/notificationsApi'
import {tokenInject} from '../../../../utils/common'
import {addAlert} from '../../../common/alerts'

const ADD_SOURCE = 'ADD_SOURCE'
const REMOVE_SOURCE = 'REMOVE_SOURCE'
const MOVE_SOURCE = 'MOVE_SOURCE'
const ADD_SCHEDULE = 'ADD_SCHEDULE'
const REMOVE_SCHEDULE = 'REMOVE_SCHEDULE'
const FILL_FORM = 'FILL_FORM'
const HISTORY = 'HISTORY'
const CHANGE_SCHEDULE = 'CHANGE_SCHEDULE'

export const THEME_TYPES = {
  PLAIN: 'plain',
  ENHANCED: 'enhanced'
}

export class NotificationForm extends ReduxModule {

  normalize (data, theme) {
    // console.log('notificationForm::normalize, input=', data)
    let newState = this.getInitialState()
    const automatic = data.automatic
    const notificationType = data.type
    const themeType = data.themeType
    const recipients = data.recipients
    const themeDiff = themeType === 'plain' ? data.plainDiff : data.enhancedDiff
    const immTheme = fromJS(theme[themeType])

    const fieldsToDelete = ['recipients', 'automatic', 'plainDiff', 'enhancedDiff', 'owner', 'type']
    fieldsToDelete.forEach(field => delete data[field])

    newState = {
      ...newState,
      ...data,
      sendUntil: data.sendUntil || '',
      notificationType
    }

    newState.scheduling.times = automatic.map(time => {
      if (time.type === 'monthly') {
        time.monthDay = (time.day === 'last') ? 'Last' : parseInt(time.day)
      }
      return time
    })

    newState.recipients = recipients.map(r => ({label: r.name, value: r.id}))

    newState = fromJS(newState)

    const paths = ['content:extract', 'content:highlightKeywords:highlight', 'content:showInfo:sourceCountry', 'content:showInfo:userComments']
    paths.forEach(path => {
      const immPath = path.split(':')
      const value = (themeDiff[path]) ? themeDiff[path] : immTheme.getIn(immPath)
      newState = newState.setIn(immPath, value)
    })

    // console.log('notificationForm::normalize, output=', newState.toJS())
    return newState
  }

  _serializeScheduleItem (time) {
    switch (time.type) {
      case 'daily':
        delete time.period
        delete time.day
        delete time.hour
        delete time.minute
        break
      case 'weekly':
        delete time.time
        delete time.days
        break
      case 'monthly':
        delete time.period
        delete time.time
        delete time.days
        time.day = (time.monthDay === 'Last') ? 'last' : time.monthDay.toString()
        break
    }
    delete time.monthDay

    return time
  }

  serialize (state, theme) {
    const data = state.toJS()
    console.log('notificationForm:serialize, input=', data)
    const scheduling = data.scheduling
    const themeType = data.themeType

    const fieldsToDelete = ['scheduling', 'showSaveAsPopup', 'id', 'content', 'isEnabledTimezone', 'isDefaultTimezone', 'sendHistory', 'active']
    fieldsToDelete.forEach(field => delete data[field])

    const automatic = scheduling.times.map(this._serializeScheduleItem)

    const sources = data.sources.map(source => {
      return {
        type: source.type,
        id: source.id
      }
    })

    const recipients = data.recipients.map(recipient => recipient.value)

    const themeDiff = {}
    const paths = ['content:extract', 'content:highlightKeywords:highlight', 'content:showInfo:sourceCountry', 'content:showInfo:userComments']
    const immTheme = fromJS(theme[themeType])
    paths.forEach(path => {
      const immPath = path.split(':')
      const themeValue = immTheme.getIn(immPath)
      const stateValue = state.getIn(immPath)
      if (themeValue !== stateValue) {
        themeDiff[path] = stateValue
      }
    })
    const enhancedDiff = themeType === 'enhanced' ? themeDiff : {}
    const plainDiff = themeType === 'plain' ? themeDiff : {}

    const alertData = {
      ...data,
      name: data.name || 'New Alert',
      theme: theme.id,
      automatic,
      recipients,
      sources,
      enhancedDiff,
      plainDiff
    }
    console.log('notificationForm:serialize, output=', alertData)
    return alertData
  }

  getHistory (notificationId, page, limit, {fulfilled, token}) {
    return getHistoryApi(token, {page, limit}, notificationId)
      .then((historyData) => {
        fulfilled(historyData)
      })
  }

  getRecipients (filter) {
    return tokenInject((dispatch, getState, token) => {
      return getReceiversApi(token, {filter})
        .then((data) => {
          const options = data.map(recipient => {
            const label = (recipient.type === 'recipient')
              ? `${recipient.name} <${recipient.email}>`
              : recipient.name
            return {value: recipient.id, label}
          })
          return {options}
        })
        .catch((errors) => {
          dispatch(addAlert(errors))
        })
    })
  };

  defineActions () {
    const changeName = this.set('NAME', 'name')
    const changeRecipients = this.set('RECIPIENTS', 'recipients')
    const changeSubject = this.set('SUBJECT', 'subject')
    const changeAutoSubject = this.set('AUTO_SUBJECT', 'automatedSubject')
    const changePublished = this.set('PUBLISHED', 'published')
    const changeAllowUnsubscribe = this.set('ALLOW_UNSUBSCRIBE', 'allowUnsubscribe')
    const changeUnsubscribeNotification = this.set('UNSUBSCRIBE_NOTIFICATION', 'unsubscribeNotification')
    const changeExtras = this.setIn('EXTRAS', ['content', 'extract'])
    const changeHighlightKeywords = this.setIn('HIGHLIGHT_KEYWORDS', ['content', 'highlightKeywords', 'highlight'])
    const changeShowSourceCountry = this.setIn('SHOW_SOURCE_COUNTRY', ['content', 'showInfo', 'sourceCountry'])
    const changeShowUserComments = this.setIn('SHOW_USER_COMMENTS', ['content', 'showInfo', 'userComments'])
    const changeThemeType = this.set('THEME_TYPE', 'themeType')
    const changeSendWhenEmpty = this.set('SEND_WHEN_EMPTY', 'sendWhenEmpty')
    const changeTimezone = this.set('TIMEZONE', 'timezone')
    const changeSendUntil = this.set('SEND_UNTIL', 'sendUntil')
    const toggleTimezone = this.toggle('TOGGLE_TIMEZONE', 'isEnabledTimezone')
    const toggleSaveAsPopup = this.toggle('TOGGLE_SAVE_AS_POPUP', 'showSaveAsPopup')
    const toggleHistory = this.toggleIn('TOGGLE_HISTORY', ['sendHistory', 'isOpen'])
    // TODO reset options for every type
    const changeScheduleType = this.setIn('SCHEDULE_TYPE', ['scheduling', 'newTime', 'type'])
    const changeNewSchedule = this.mergeIn('CHANGE_NEW_SCHEDULE', ['scheduling', 'newTime'])
    const clearForm = this.resetToInitialState('CLEAR')

    const addSource = this.createAction(ADD_SOURCE, source => source)
    const removeSource = this.createAction(REMOVE_SOURCE, sourceId => sourceId)
    const moveSource = this.createAction(MOVE_SOURCE, (sourceId, isUp) => ({sourceId, isUp}))
    const addSchedule = this.createAction(ADD_SCHEDULE)
    const removeSchedule = this.createAction(REMOVE_SCHEDULE, id => id)
    const fillForm = this.createAction(FILL_FORM, (item, theme) => ({item, theme}))
    const changeExistingSchedule = this.createAction('CHANGE_SCHEDULE', (item, id) => ({item, id}))

    this.setPending = this.set('LOADING', 'isLoading')

    // const historyPending = this.setIn(`${HISTORY} pending`, ['sendHistory', 'isPending'])
    const getHistory = this.thunkAction(HISTORY, this.getHistory, true)

    return {
      changeName,
      changeRecipients,
      changeSubject,
      changeAutoSubject,
      changeAllowUnsubscribe,
      changeUnsubscribeNotification,
      changeExtras,
      changeHighlightKeywords,
      changeShowSourceCountry,
      changeShowUserComments,
      changeThemeType,
      changeSendWhenEmpty,
      changePublished,
      addSource,
      removeSource,
      addSchedule,
      removeSchedule,
      moveSource,
      changeTimezone,
      toggleTimezone,
      changeScheduleType,
      changeNewSchedule,
      changeExistingSchedule,
      changeSendUntil,
      fillForm,
      clearForm,
      getRecipients: this.getRecipients,
      toggleSaveAsPopup,
      toggleHistory,
      getHistory
    }

  }

  defineReducers () {

    return {
      [ADD_SOURCE]: (state, {payload: source}) => {
        const sources = state.get('sources').push(fromJS(source))
        return state.set('sources', sources)
      },

      [REMOVE_SOURCE]: (state, {payload: sourceId}) => {
        const sources = state.get('sources').filter(source => source.get('id') !== sourceId)
        return state.set('sources', sources)
      },

      [MOVE_SOURCE]: (state, {payload}) => {
        const { sourceId, isUp } = payload
        let sources = state.get('sources')

        const fromIndex = sources.findIndex(source => source.get('id') === sourceId)
        const toIndex = (isUp) ? fromIndex - 1 : fromIndex + 1

        const isUpFail = isUp && (toIndex < 0)
        const isDownFail = !isUp && (toIndex >= sources.size)
        if (isUpFail || isDownFail) {
          return state
        }

        const source = sources.get(fromIndex)
        sources = sources
          .splice(fromIndex, 1)
          .splice(toIndex, 0, source)

        return state.set('sources', sources)
      },

      [ADD_SCHEDULE]: (state) => {
        const path = ['scheduling', 'times']
        const item = state.getIn(['scheduling', 'newTime'])
        const items = state.getIn(path).push(item)
        return state.setIn(path, items)
      },

      [REMOVE_SCHEDULE]: (state, {payload: id}) => {
        const path = ['scheduling', 'times']
        const items = state.getIn(path).filter((item, index) => index !== id)
        return state.setIn(path, items)
      },

      [FILL_FORM]: (state, {payload}) => {
        const { item, theme } = payload
        return this.normalize(item, theme)
      },

      [[`${HISTORY} fulfilled`]]: (state, {payload}) => {
        const { data, limit, page, totalCount } = payload
        const entities = state.getIn(['sendHistory', 'entities']).concat(data)
        return state.mergeIn(['sendHistory'], {
          entities,
          limit,
          page,
          totalCount,
          isLoadingCompleted: true
        })
      },

      [[`${HISTORY} pending`]]: (state, {payload}) => {
        return state.mergeIn(['sendHistory'], {
          isPending: payload.isPending
        })
      },

      [CHANGE_SCHEDULE]: (state, {payload: {item, id}}) => {
        return state.mergeIn(['scheduling', 'times', id], {
          item
        })
      }
    }
  }

}
