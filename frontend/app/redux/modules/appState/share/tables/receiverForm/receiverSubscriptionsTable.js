import ReceiverFormTable from './receiverFormTable'
import * as api from '../../../../../../api/notificationsApi'

class ReceiverSubscriptionsTable extends ReceiverFormTable {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Receiver form subscriptions table]'
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'receiverForm', 'subscriptions'])
  }

  getLoadTableRequestPayload (tableState, receiver) {
    let payload = super.getLoadTableRequestPayload(tableState)
    if (receiver) {
      payload.entityId = receiver.id
    }
    return payload
  }

  getDataFromResponse (response, receiver) {
    const data = response.notifications
    this.addDataColumn(data.data, 'subscribed', receiver.subscriptions)
    return data
  }

}

const instance = new ReceiverSubscriptionsTable()
instance.init()
export default instance
