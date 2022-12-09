import { getCurrentTimezone } from '../../../../../common/Timezones'
import { createItem, updateItem } from '../../../../../api/notificationsApi'
import {NotificationForm, THEME_TYPES} from './notificationForm'
import {addAlert} from '../../../common/alerts'
import {getRestrictions} from '../../../common/auth'
import {switchShareSubScreen, NOTIFICATION_SUBSCREENS} from '../tabs'

export const EXTRAS = {
  CONTEXTUAL: 'context',
  START: 'start',
  NO: 'no'
}

export class AlertForm extends NotificationForm {

  getNamespace () {
    return '[Alert Form]'
  }

  getInitialState () {
    return {
      name: 'New Alert',
      recipients: [],
      subject: '',
      automatedSubject: false,
      published: false,
      allowUnsubscribe: false,
      unsubscribeNotification: false,
      sources: [],
      content: {
        extract: EXTRAS.CONTEXTUAL,
        highlightKeywords: {
          highlight: false
        },
        showInfo: {
          sourceCountry: false,
          userComments: 'no'
        }
      },
      themeType: THEME_TYPES.PLAIN,
      sendWhenEmpty: false,
      timezone: getCurrentTimezone(),
      isEnabledTimezone: false,
      isDefaultTimezone: true,
      sendUntil: '',
      scheduling: {
        constants: {
          type: ['daily', 'weekly', 'monthly'],
          time: ['15m', '30m', '1h', '2h', '3h', '4h', '6h', '12h', 'once'],
          days: ['all', 'weekdays', 'weekends'],
          period: ['every', 'first', 'second', 'third', 'fourth', 'last'],
          day: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
          monthDay: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 'Last'],
          hour: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
          minute: [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55]
        },
        newTime: {
          type: 'daily',
          time: '15m',
          days: 'all',
          period: 'every',
          monthDay: 1,
          day: 'monday',
          hour: 13,
          minute: 0
        },
        times: []
      },
      showSaveAsPopup: false,
      sendHistory: {
        isOpen: false,
        isPending: false,
        isLoadingCompleted: false,
        page: 0,
        limit: 5,
        totalCount: 0,
        entities: []
      }
    }
  }

  serialize (data, theme) {
    let serialized = super.serialize(data, theme)
    return {
      ...serialized,
      notificationType: 'alert'
    }
  }

  saveAlert = (isEdit, {dispatch, getState, token}) => {
    const data = getState().getIn(['appState', 'share', 'forms', 'alert'])
    const id = data.get('id')
    const theme = getState().getIn(['appState', 'share', 'themes', 'defaultTheme'])
    const alertData = this.serialize(data, theme)
    const promise = isEdit ? updateItem(token, alertData, id) : createItem(token, alertData)
    return promise
      .then((data) => {
        dispatch(getRestrictions())
        dispatch(addAlert({type: 'notice', transKey: 'alertSaved'}))
        dispatch(switchShareSubScreen('notifications', NOTIFICATION_SUBSCREENS.TABLES))
      })
  };

  defineActions () {
    const actions = super.defineActions()
    const saveAlert = this.thunkAction('SAVE_ALERT', this.saveAlert)
    return {
      ...actions,
      saveAlert
    }
  }

}

const instance = new AlertForm()
instance.init()
export default instance
