import ReceiverFormTable from './receiverFormTable'
import * as api from '../../../../../../api/groupsApi'

///api/v1/recipients/groups with recipientId

class ReceiverGroupsTable extends ReceiverFormTable {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Recipient form groups table]'
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'receiverForm', 'groups'])
  }

  getLoadTableRequestPayload (tableState, receiver) {
    let payload = super.getLoadTableRequestPayload(tableState)
    if (receiver) {
      payload.recipientId = receiver.id
    }
    return payload
  }

  getDataFromResponse (response, receiver) {
    const data = response.groups
    this.addDataColumn(data.data, 'enrolled', receiver.groups)
    return data
  }

}

const instance = new ReceiverGroupsTable()
instance.init()
export default instance
