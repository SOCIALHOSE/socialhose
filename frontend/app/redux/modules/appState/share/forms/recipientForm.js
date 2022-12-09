import {ReceiverForm} from './receiverForm'
import {
  switchShareSubScreen, switchShareTable, RECEIVER_SUBSCREENS, RECEIVER_TABLES, RECIPIENT_FORM_TABLES
} from '../tabs'
import {fromJS} from 'immutable'
import * as api from '../../../../../api/recipientsApi'
import {addAlert} from '../../../common/alerts'

const SAVE_RECIPIENT = 'Save recipient'

export class RecipientForm extends ReceiverForm {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Recipient Form]'
  }

  serialize (data) {
    return {
      firstName: data.firstName,
      lastName: data.lastName,
      email: data.email,
      active: data.active,
      notifications: data.subscriptions,
      groups: data.groups
    }
  };

  normalize (data) {
    let newState = this.getInitialState()
    newState = {
      ...newState,
      id: data.id,
      firstName: data.firstName,
      lastName: data.lastName,
      email: data.email,
      active: data.active,
      groups: data.groups.map(group => group.id),
      subscriptions: data.subscriptions.ids
    }
    return fromJS(newState)
  };

  saveRecipient = ({dispatch, fulfilled, getState, token}) => {
    const data = getState().getIn(['appState', 'share', 'forms', 'recipient']).toJS()
    const id = data.id

    if (data.firstName && data.lastName && data.email) {
      const payload = this.serialize(data)
      const apiRequest = id ? api.updateItem(token, payload, id) : api.createItem(token, payload)
      return apiRequest.then(() => {
        fulfilled()
        dispatch(addAlert({type: 'notice', transKey: 'recipientSaved'}))
        dispatch(switchShareSubScreen('recipients', RECEIVER_SUBSCREENS.TABLES))
        dispatch(switchShareTable('recipients', RECEIVER_TABLES.RECIPIENTS))
      })

    } else {
      dispatch(addAlert({type: 'error', transKey: 'recipientNamesEmpty'}))
    }
  };

  defineActions () {
    const saveReceiver = this.thunkAction(SAVE_RECIPIENT, this.saveRecipient, true)

    return {
      ...super.defineActions(),
      saveReceiver,
      deleteReceiver: this.deleteRecipient
    }
  }

  getTableTabs () {
    return [RECIPIENT_FORM_TABLES.SUBSCRIPTIONS, RECIPIENT_FORM_TABLES.GROUPS, RECIPIENT_FORM_TABLES.EMAIL_HISTORY]
  }

  getInitialState () {
    const state = super.getInitialState()
    return {
      ...state,
      creationDate: '',
      firstName: '',
      lastName: '',
      email: '',
      active: true,
      subscriptions: [],
      groups: []
    }
  }

}

const instance = new RecipientForm()
instance.init()
export default instance
