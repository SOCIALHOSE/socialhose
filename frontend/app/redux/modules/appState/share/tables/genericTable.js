import ReduxModule from '../../../abstract/reduxModule'
import { addAlert } from '../../../common/alerts'
import {tokenInject} from '../../../../utils/common'
import {getRestrictions} from '../../../common/auth'

const SELECT_TABLE_ROW = 'Select table row'
const SELECT_TABLE_ALL_ROWS = 'Select all rows'
const SET_TABLE_PARAMS = 'Set table parameters'
const LOAD_TABLE = 'Load table'
const CONFIRM_DELETE = 'Confirm delete'
const CANCEL_DELETE = 'Cancel delete'
const CHANGE_FILTERED = 'Change filtered'
const TOGGLE_FIELD = 'Toggle field'
const ASYNC_ACTION = 'Async action'
const DELETE_ITEMS = 'Delete items'

export default class GenericTableModule extends ReduxModule {

  constructor (api) {
    super()
    this.api = api
    this.updateRestrictionsAfterDelete = false
  }

  /*
  state - full state
  return - table state
   */
  getTableState (state) {
    //implement in subclasses
  }

  asyncToggleFieldAction (apiMethod, optionName) {
    return (ids, optionValue) => {
      return tokenInject((dispatch, getState, token) => {
        const payload = {
          ids,
          [optionName]: optionValue
        }

        dispatch(this.asyncActionPending(true))
        apiMethod(token, payload)
          .then(() => {
            dispatch(this.toggleField(ids, optionName, optionValue))
            dispatch(this.asyncActionPending(false))
          })
          .catch((error) => {
            dispatch(this.asyncActionPending(false))
            dispatch(addAlert(error))
          })

      })
    }
  }

  _fixForDeleteFromLastPage (dispatch, getState, ids) {
    const tableState = this.getTableState(getState()).toJS()
    const totalPages = Math.ceil(tableState.totalCount / tableState.limit)
    if (tableState.page === totalPages && totalPages !== 1) { //we are on the last page
      const itemsOnLastPage = tableState.totalCount % tableState.limit
      const itemsToDelete = Object.keys(ids).length
      if (itemsOnLastPage === itemsToDelete) { //and we are deleting everything
        const newPage = tableState.page - 1
        dispatch(this.setTableParams({page: newPage}))
      }
    }
  }

  deleteItems = (ids, {token, fulfilled, getState, dispatch}) => {
    return this.api
      .deleteItems(token, {ids})
      .then(() => {
        this._fixForDeleteFromLastPage(dispatch, getState, ids)
        const payload = this._getLoadPayload(getState)
        this.updateRestrictionsAfterDelete && dispatch(getRestrictions())
        return this.api
          .getItems(token, payload)
          .then((response) => {
            fulfilled(this.getDataFromResponse(response))
          })
      })
  };

  _getLoadPayload (getState) {
    const state = getState()
    const tableState = this.getTableState(state).toJS()
    return this.getLoadTableRequestPayload(tableState)
  }

  getLoadTableRequestPayload (tableState) {
    const payload = {
      page: tableState.page,
      limit: tableState.limit,
      sortField: tableState.sortField,
      sortDirection: tableState.sortDirection
    }
    if (tableState.filter) {
      payload.filter = tableState.filter
    }
    return payload
  }

  getDataFromResponse (response) {
    return response['notifications']
  }

  getItems = (token, payload) => {
    return this.api.getItems(token, payload)
  }

  loadTable = (params, {dispatch, getState, token, fulfilled}) => {
    if (params) {
      dispatch(this.setTableParams(params))
    }
    const payload = this._getLoadPayload(getState)
    return this.getItems(token, payload)
      .then((response) => {
        fulfilled(this.getDataFromResponse(response))
      })
  };

  defineActions () {

    const selectTableRow = this.createAction(SELECT_TABLE_ROW, (itemId) => itemId)
    const selectTableAllRows = this.createAction(SELECT_TABLE_ALL_ROWS)
    const confirmDelete = this.createAction(CONFIRM_DELETE)
    const cancelDelete = this.createAction(CANCEL_DELETE)
    const changeTableFiltered = this.createAction(CHANGE_FILTERED)

    const setTableParams = this.createAction(SET_TABLE_PARAMS, (params) => params)
    this.setTableParams = setTableParams
    const loadTable = this.thunkAction(LOAD_TABLE, this.loadTable, true)
    const deleteItems = this.thunkAction(DELETE_ITEMS, this.deleteItems, true)

    this.asyncActionPending = this.createAction(`${ASYNC_ACTION} pending`, (value) => ({isPending: value}))
    this.toggleField = this.createAction(TOGGLE_FIELD, (ids, fieldName, fieldValue) => ({ids, fieldName, fieldValue}))

    const toggleActive = this.asyncToggleFieldAction(this.api.activateItems, 'active')
    const togglePublish = this.asyncToggleFieldAction(this.api.publishItems, 'published')

    return {
      loadTable,
      selectTableRow,
      selectTableAllRows,
      setTableParams,
      confirmDelete,
      cancelDelete,
      changeTableFiltered,
      toggleActive,
      togglePublish,
      deleteItems
    }
  }

  getInitialState () {
    return {
      page: 1,
      limit: 10,
      sortField: 'name',
      sortDirection: 'asc',
      data: [],
      count: 0,
      totalCount: 0,
      isLoading: false,
      isAllSelected: false,
      selectedIds: [],
      idsToDelete: {},
      isDeletePopupVisible: false
    }
  }

  /** REDUCERS **/

  onSelectTableRow (state, {payload: itemId}) {
    let selectedIds = state.get('selectedIds')
    const isSelected = selectedIds.includes(itemId)
    if (isSelected) {
      selectedIds = selectedIds.filter(id => id !== itemId)
    }
    else {
      selectedIds = selectedIds.push(itemId)
    }
    return state.merge({
      'selectedIds': selectedIds,
      'isAllSelected': false
    })
  }

  onSelectAllTableRows (state) {
    const isAllSelected = state.get('isAllSelected')
    if (isAllSelected) { //then deselect all
      return state.merge({
        isAllSelected: false,
        selectedIds: []
      })
    }
    else { //select all currently loaded data
      const selectedIds = state.get('data').map(item => item.get('id'))
      return state.merge({
        isAllSelected: true,
        selectedIds
      })
    }
  }

  onLoadFulfilled (state, {payload: response}) {
    return state.merge({
      data: response.data,
      count: response.count,
      totalCount: response.totalCount
    })
  }

  onDeleteItemsFulfilled (state, {payload: response}) {
    return state.merge({
      data: response.data,
      count: response.count,
      totalCount: response.totalCount,
      selectedIds: []
    })
  };

  onConfirmDelete (state, {payload: ids}) {
    return state.merge({
      isDeletePopupVisible: true,
      idsToDelete: ids
    })
  }

  onCancelDelete (state) {
    return state.merge({
      isDeletePopupVisible: false,
      idsToDelete: {}
    })
  }

  onAsyncActionPending (state, {payload: {isPending}}) {
    return state.merge({
      isLoading: isPending,
      isDeletePopupVisible: false //dirty hack, need to think 'bout it
    })
  }

  onToggleField (state, {payload: {ids, fieldName, fieldValue}}) {
    let tableData = state.get('data')
    const newValues = {[fieldName]: fieldValue}
    tableData = tableData.map((item) => (ids.includes(item.get('id'))) ? item.merge(newValues) : item)
    return state.set('data', tableData)
  }

  defineReducers () {
    return {
      [SELECT_TABLE_ROW]: this.onSelectTableRow,
      [SELECT_TABLE_ALL_ROWS]: this.onSelectAllTableRows,
      [SET_TABLE_PARAMS]: this.mergeReducer(),
      [`${LOAD_TABLE} pending`]: this.thunkPendingReducer('isLoading'),
      [`${LOAD_TABLE} fulfilled`]: this.onLoadFulfilled,
      [`${ASYNC_ACTION} pending`]: this.onAsyncActionPending,
      [`${DELETE_ITEMS} pending`]: this.onAsyncActionPending,
      [`${DELETE_ITEMS} fulfilled`]: this.onDeleteItemsFulfilled,
      [TOGGLE_FIELD]: this.onToggleField,
      [CONFIRM_DELETE]: this.onConfirmDelete,
      [CANCEL_DELETE]: this.onCancelDelete,
      [CHANGE_FILTERED]: this.setReducer('filtered')
    }
  }

}
