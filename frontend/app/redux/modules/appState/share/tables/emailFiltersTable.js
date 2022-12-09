import GenericTable from './genericTable'
import * as api from '../../../../../api/notificationsApi'

class EmailFiltersTable extends GenericTable {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Email filters]'
  }

  getItems = (token, payload) => {
    return this.api.getFilters(token, payload)
  };

  getDataFromResponse (response) {
    return response['filters']
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'emailFilters'])
  }

  getLoadTableRequestPayload (tableState) {
    return {
      ...super.getLoadTableRequestPayload(tableState),
      type: tableState.filterType
    }
  }

  getInitialState () {
    return {
      ...super.getInitialState(),
      filterType: 'owner'
    }
  }

}

const instance = new EmailFiltersTable()
instance.init()
export default instance
