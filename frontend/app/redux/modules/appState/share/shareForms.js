import {tokenInject} from '../../../utils/common'
import {
  NOTIFICATION_SUBSCREENS, RECEIVER_SUBSCREENS, switchShareSubScreen,
  NOTIFICATION_TABLES
} from './tabs'
import { addAlert } from '../../common/alerts'
import { getDefaultTheme } from './emailThemes/themes'

import myEmailsTable from './tables/myEmailsTable'
import publishedEmailsTable from './tables/publishedEmailsTable'
import emailsTable from './tables/emailsTable'

import alertForm from './forms/alertForm'
import newsletterForm from './forms/newsletterForm'
import recipientForm from './forms/recipientForm'
import groupForm from './forms/groupForm'

import * as notificationsApi from '../../../../api/notificationsApi'

const tablePendingActions = {
  [NOTIFICATION_TABLES.MY_EMAILS]: myEmailsTable.asyncActionPending,
  [NOTIFICATION_TABLES.PUBLISHED]: publishedEmailsTable.asyncActionPending,
  emails: emailsTable.asyncActionPending
}

//**** ACTIONS ****//

const loadTablePending = (type, isPending) => {
  return tablePendingActions[type](isPending)
}

const startEditNotification = (notification, table, tab = 'notifications') => {
  return tokenInject((dispatch, getState, token) => {
    if (notification.type === 'alert') {

      dispatch(loadTablePending(table, true))

      dispatch(getDefaultTheme())
        .then((defaultTheme) => {
          return notificationsApi.getItem(token, null, notification.id)
            .then((fullNotification) => {
              dispatch(loadTablePending(table, false))
              dispatch(alertForm.actions.fillForm(fullNotification, defaultTheme))
              dispatch(switchShareSubScreen(tab, NOTIFICATION_SUBSCREENS.ALERT_FORM))
            })
        })
        .catch((errors) => {
          dispatch(loadTablePending(table, false))
          dispatch(addAlert(errors))
        })
    }
  })
}

const startCreateNotification = (type, table, tab = 'notifications') => {
  return (dispatch, getState) => {
    
    const recipient = getState().getIn(['common', 'auth', 'user', 'recipient']);
    if (!recipient) {
      return dispatch(addAlert('Please create at least one recipient from Manage Recipients to create an alert.'));
    }
    const myself = recipient.toJS()
    const defaultRecipient = {
      value: myself.id,
      label: `${myself.firstName} ${myself.lastName} <${myself.email}>`
    }
    
    dispatch(loadTablePending(table, true))
    dispatch(getDefaultTheme())
      .then(() => {
        dispatch(loadTablePending(table, false))
        if (type === NOTIFICATION_SUBSCREENS.ALERT_FORM) {
          dispatch(alertForm.actions.clearForm())
          dispatch(alertForm.actions.changeRecipients([defaultRecipient]))
          dispatch(switchShareSubScreen(tab, NOTIFICATION_SUBSCREENS.ALERT_FORM))
        }
        else if (type === NOTIFICATION_SUBSCREENS.NEWSLETTER_FORM) {
          dispatch(newsletterForm.actions.clearForm())
          dispatch(switchShareSubScreen(tab, NOTIFICATION_SUBSCREENS.NEWSLETTER_FORM))
        }
      })
      .catch((errors) => {
        dispatch(loadTablePending(table, false))
        dispatch(addAlert(errors))
      })
  }
}

const startCreateRecipient = () => {
  return (dispatch) => {
    dispatch(switchShareSubScreen('recipients', RECEIVER_SUBSCREENS.RECIPIENT_FORM))
    dispatch(recipientForm.actions.clearForm())
  }
}

const startCreateGroup = () => {
  return (dispatch) => {
    dispatch(switchShareSubScreen('recipients', RECEIVER_SUBSCREENS.GROUP_FORM))
    dispatch(groupForm.actions.clearForm())
  }
}

const startEditRecipient = (item) => {
  console.log('start edit')
  return (dispatch) => {
    dispatch(switchShareSubScreen('recipients', RECEIVER_SUBSCREENS.RECIPIENT_FORM))
    dispatch(recipientForm.actions.clearForm())
    dispatch(recipientForm.actions.fillForm(item))
  }
}

const startEditGroup = (item) => {
  return (dispatch) => {
    dispatch(switchShareSubScreen('recipients', RECEIVER_SUBSCREENS.GROUP_FORM))
    dispatch(groupForm.actions.clearForm())
    dispatch(groupForm.actions.fillForm(item))
  }
}

export const actions = {
  startEditNotification,
  startCreateNotification,
  startCreateRecipient,
  startCreateGroup,
  startEditRecipient,
  startEditGroup
}

//**** This module has no state (: ****//

