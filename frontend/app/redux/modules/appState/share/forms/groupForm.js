import {ReceiverForm} from './receiverForm'
import {
  GROUP_FORM_TABLES, switchShareSubScreen, switchShareTable, RECEIVER_TABLES, RECEIVER_SUBSCREENS
} from '../tabs'
import {fromJS} from 'immutable'
import * as api from '../../../../../api/groupsApi'
import {addAlert} from '../../../common/alerts'

const SAVE_GROUP = 'Save group'

export class GroupForm extends ReceiverForm {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Group Form]'
  }

  serialize (data) {
    return {
      name: data.name,
      description: data.description,
      active: data.active,
      recipients: data.recipients,
      notifications: data.subscriptions
    }
  };

  normalize (data) {
    let newState = this.getInitialState()
    newState = {
      ...newState,
      id: data.id,
      name: data.name,
      description: data.description || '',
      active: data.active,
      recipients: data.recipients,
      subscriptions: data.subscriptions.ids
    }
    return fromJS(newState)
  };

  saveGroup = ({dispatch, fulfilled, getState, token}) => {
    const data = getState().getIn(['appState', 'share', 'forms', 'group']).toJS()
    const id = data.id

    if (data.name) {
      const payload = this.serialize(data)
      const apiRequest = id ? api.updateItem(token, payload, id) : api.createItem(token, payload)
      return apiRequest.then(() => {
        fulfilled()
        dispatch(addAlert({type: 'notice', transKey: 'groupSaved'}))
        dispatch(switchShareSubScreen('recipients', RECEIVER_SUBSCREENS.TABLES))
        dispatch(switchShareTable('recipients', RECEIVER_TABLES.GROUPS))
      })

    } else {
      dispatch(addAlert({type: 'error', transKey: 'groupNameEmpty'}))
    }
  };

  defineActions () {
    const saveReceiver = this.thunkAction(SAVE_GROUP, this.saveGroup, true)

    return {
      ...super.defineActions(),
      saveReceiver,
      deleteReceiver: this.deleteGroup
    }
  }

  getTableTabs () {
    return [GROUP_FORM_TABLES.RECIPIENTS, GROUP_FORM_TABLES.SUBSCRIPTIONS, GROUP_FORM_TABLES.EMAIL_HISTORY]
  }

  getInitialState () {
    const state = super.getInitialState()
    return {
      name: '',
      description: '',
      creationDate: '',
      recipients: [],
      subscriptions: [],
      ...state
    }
  }

}

const instance = new GroupForm()
instance.init()
export default instance
