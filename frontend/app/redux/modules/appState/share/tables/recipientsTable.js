import GenericTable from './genericTable'
import * as api from '../../../../../api/recipientsApi'

class RecipientsTable extends GenericTable {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Recipients table]'
  }

  getInitialState () {
    const state = super.getInitialState()
    return {
      ...state,
      filter: ''
    }
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'recipients'])
  }

  getDataFromResponse (response) {
    return response['recipients']
  }

}

const instance = new RecipientsTable()
instance.init()
export default instance
