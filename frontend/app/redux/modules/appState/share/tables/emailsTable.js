import GenericTable from './genericTable'
import * as api from '../../../../../api/notificationsApi'

const SET_FILTER = 'Set filter'
const CLEAR_FILTER = 'Clear filter'

class EmailsTable extends GenericTable {

  constructor () {
    super(api)
    this.updateRestrictionsAfterDelete = true
  }

  getNamespace () {
    return '[Emails]'
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'emails'])
  }

  getItems = (token, payload) => {
    return this.api.getAllItems(token, payload)
  }

  getLoadTableRequestPayload (tableState) {
    const payload = super.getLoadTableRequestPayload(tableState)
    if (tableState.filter) {
      payload.filterId = tableState.filter.id
      payload.filterType = tableState.filter.type
    }
    delete payload.filter
    return payload
  }

  defineActions () {

    const setFilter = this.createAction(SET_FILTER)
    const clearFilter = this.createAction(CLEAR_FILTER)

    return {
      ...super.defineActions(),
      setFilter,
      clearFilter
    }
  }

  getInitialState () {
    return {
      ...super.getInitialState(),
      filter: null
    }
  }

  defineReducers () {
    return {
      ...super.defineReducers(),
      [SET_FILTER]: this.setReducer('filter'),
      [CLEAR_FILTER]: this.resetReducer('filter', null)
    }
  }

}

const instance = new EmailsTable()
instance.init()
export default instance
