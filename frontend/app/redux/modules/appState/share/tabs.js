import { createAction, handleActions } from 'redux-actions'
import { fromJS } from 'immutable'

//**** CONSTANTS ****//
const NS = '[Share tab]'
const SWITCH_SUBSCREEN = `${NS} Switch subscreen`
const SWITCH_TABLE = `${NS} Switch table`

//**** ACTIONS ****//
export const switchShareSubScreen = createAction(SWITCH_SUBSCREEN, (type, subScreen) => ({type, subScreen}))
export const switchShareTable = createAction(SWITCH_TABLE, (type, table) => ({type, table}))

export const actions = {
  switchShareSubScreen,
  switchShareTable
}

/* TABS SUBSCREENS */
export const NOTIFICATION_SUBSCREENS = {
  TABLES: 'tables',
  ALERT_FORM: 'alert',
  NEWSLETTER_FORM: 'newsletter'
}

export const RECEIVER_SUBSCREENS = {
  TABLES: 'tables',
  RECIPIENT_FORM: 'recipient',
  GROUP_FORM: 'group'
}

export const EMAILS_SUBSCREENS = {
  EMAILS_TABLE: 'table',
  ALERT_FORM: 'alert',
  NEWSLETTER_FORM: 'newsletter',
  FILTERS_TABLE: 'filters'
}

/* TABLES IN 'tables' SUBSCREEN */
export const NOTIFICATION_TABLES = {
  MY_EMAILS: 'myEmails',
  PUBLISHED: 'publishedEmails'
}

export const RECEIVER_TABLES = {
  RECIPIENTS: 'recipients',
  GROUPS: 'groups'
}

/* TABLES IN FORMS */
export const RECIPIENT_FORM_TABLES = {
  EMAIL_HISTORY: 'emailHistory',
  SUBSCRIPTIONS: 'subscriptions',
  GROUPS: 'groups'
}

export const GROUP_FORM_TABLES = {
  EMAIL_HISTORY: 'emailHistory',
  SUBSCRIPTIONS: 'subscriptions',
  RECIPIENTS: 'recipients'
}

export const initialState = fromJS({
  notifications: {
    subScreenVisible: NOTIFICATION_SUBSCREENS.TABLES,
    tableVisible: 'myEmails'
  },
  recipients: {
    subScreenVisible: RECEIVER_SUBSCREENS.TABLES,
    tableVisible: 'recipients'
  },
  emails: {
    subScreenVisible: EMAILS_SUBSCREENS.EMAILS_TABLE
  }
})

//**** REDUCERS ****//
export default handleActions({
  [SWITCH_SUBSCREEN]: (state, {payload}) => {
    const { type, subScreen } = payload
    return state.setIn([type, 'subScreenVisible'], subScreen)
  },

  [SWITCH_TABLE]: (state, {payload}) => {
    const { type, table } = payload
    return state.setIn([type, 'tableVisible'], table)
  }
}, initialState)
