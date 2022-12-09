import GenericTable from './genericTable'
import * as api from '../../../../../api/notificationsApi'

class MyEmailsTable extends GenericTable {

  constructor () {
    super(api)
    this.updateRestrictionsAfterDelete = true
  }

  getNamespace () {
    return '[My Emails]'
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'myEmails'])
  }
  
  getLoadTableRequestPayload (tableState) {
    return {
      ...super.getLoadTableRequestPayload(tableState),
      onlyPublished: false
    }
  }

}

const instance = new MyEmailsTable()
instance.init()
export default instance
