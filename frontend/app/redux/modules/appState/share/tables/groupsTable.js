import GenericTable from './genericTable'
import * as api from '../../../../../api/groupsApi'

class GroupsTable extends GenericTable {

  constructor () {
    super(api)
  }

  getNamespace () {
    return '[Groups table]'
  }

  getInitialState () {
    const state = super.getInitialState()
    return {
      ...state,
      filter: ''
    }
  }

  getDataFromResponse (response) {
    return response['groups']
  }

  getTableState (state) {
    return state.getIn(['appState', 'share', 'tables', 'groups'])
  }

}

const instance = new GroupsTable()
instance.init()
export default instance
