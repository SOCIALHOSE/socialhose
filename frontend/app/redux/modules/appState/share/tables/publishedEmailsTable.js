import GenericTableModule from './genericTable'
import * as api from '../../../../../api/notificationsApi'

class PublishedEmailsTable extends GenericTableModule {

  constructor () {
    super(api)
    this.updateRestrictionsAfterDelete = true
  }

  getNamespace () {
    return '[Published Emails]'
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'publishedEmails'])
  }

  getLoadTableRequestPayload (tableState) {
    return {
      ...super.getLoadTableRequestPayload(tableState),
      onlyPublished: true
    }
  }

  defineActions () {
    const toggleSubscribe = this.asyncToggleFieldAction(this.api.subscribeItems, 'subscribed')
    return {
      ...super.defineActions(),
      toggleSubscribe
    }
  }

}

const instance = new PublishedEmailsTable()
instance.init()
export default instance
