import ReceiverFormTable from './receiverFormTable'
import * as api from '../../../../../../api/recipientsApi'

///api/v1/recipients with groupId

class ReceiverRecipientsTable extends ReceiverFormTable {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Group form recipients table]'
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'receiverForm', 'recipients'])
  }

  getLoadTableRequestPayload (tableState, receiver) {
    let payload = super.getLoadTableRequestPayload(tableState)
    if (receiver) {
      payload.groupId = receiver.id
    }
    return payload
  }

  getDataFromResponse (response, receiver) {
    const data = response['recipients']
    this.addDataColumn(data.data, 'enrolled', receiver.recipients)
    return data
  }
}

const instance = new ReceiverRecipientsTable()
instance.init()
export default instance
