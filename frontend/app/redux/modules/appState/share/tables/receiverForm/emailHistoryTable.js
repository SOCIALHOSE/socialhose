import * as api from '../../../../../../api/receiversApi'
import GenericTableModule from '../genericTable'

class EmailHistory extends GenericTableModule {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Email history]'
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'receiverForm', 'emailHistory'])
  }

  loadTable = (params, receiver, {dispatch, getState, token, fulfilled}) => {
    if (params) {
      dispatch(this.setTableParams(params))
    }
    const payload = this._getLoadPayload(getState)
    return this.api
      .getEmailHistory(token, payload, receiver.id)
      .then((response) => {
        fulfilled(response)
      })
  };

}

const instance = new EmailHistory()
instance.init()
export default instance
