import {fromJS} from 'immutable'
import GenericTableModule from '../genericTable'

const TOGGLE_SUBSCRIBED = 'Toggle subscribed'
const TOGGLE_ENROLLED = 'Toggle enrolled'

export default class ReceiverFormTable extends GenericTableModule {

  getDataFromResponse (response) {
    //implement in subclasses
  }

  getLoadTableRequestPayload (tableState, receiver) {
    let payload = super.getLoadTableRequestPayload(tableState)
    if (tableState.filter) {
      payload.filter = tableState.filter
    }
    const statusFilter = tableState.statusFilter
    if (statusFilter) {
      payload.statusFilter = tableState.statusFilter
    }
    return payload
  }

  _getLoadPayload (getState, receiver) {
    const tableState = this.getTableState(getState()).toJS()
    return this.getLoadTableRequestPayload(tableState, receiver)
  }

  loadTable = (params, receiver, {dispatch, getState, token, fulfilled}) => {
    if (params) {
      dispatch(this.setTableParams(params))
    }
    const payload = this._getLoadPayload(getState, receiver) // <-- difference from genericTable
    return this.api
      .getItems(token, payload)
      .then((response) => {
        fulfilled(this.getDataFromResponse(response, receiver))
      })
  };

  addDataColumn (data, fieldName, ids = []) {
    data.forEach((item) => {
      item[fieldName] = ids.includes(item.id)
    })
  };

  toggleDataField (actionName, fieldName) {
    return this.createHandler(
      actionName,
      (itemId, turnOn) => ({itemId, turnOn}),
      (state, {payload: {itemId, turnOn}}) => {
        const tableData = state.get('data').toJS()
        tableData.forEach((item) => {
          if (item.id === itemId) {
            item[fieldName] = turnOn
          }
        })
        return state.set('data', fromJS(tableData))
      }
    )
  }

  defineActions () {
    const actions = super.defineActions()
    const toggleSubscribed = this.toggleDataField(TOGGLE_SUBSCRIBED, 'subscribed')
    const toggleEnrolled = this.toggleDataField(TOGGLE_ENROLLED, 'enrolled')

    return {
      ...actions,
      toggleSubscribed,
      toggleEnrolled
    }
  }

  getInitialState () {
    return {
      ...super.getInitialState(),
      filter: '',
      statusFilter: 'all'
    }
  }

}
