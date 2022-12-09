/** ----------------CONSTANTS---------------- **/
import {createAction, handleActions} from 'redux-actions'
import {fromJS, Map} from 'immutable'

import {getSavedAnalysesApi, deleteSavedAnalysesApi} from '../../../api/analyticApi'
import {thunkAction, tokenInject} from '../../utils/common'

const GET_RECENT_ANALYSIS = 'GET_RECENT_ANALYSIS'

const CONFIRM_DELETE_ANALYSES = 'CONFIRM_DELETE_ANALYSES'
const CANCEL_DELETE_ANALYSES = 'CANCEL_DELETE_ANALYSES'
const DELETE_ANALYSES = 'DELETE_ANALYSES'

const SELECT_TABLE_ROW = 'SELECT_ANALYSES_TABLE_ROW'
const SELECT_ALL_ROWS = 'SELECT_ANALYSES_TABLE_ALL_ROWS'
const SET_TABLE_PARAMS = 'SET_ANALYSES_TABLE_PARAMS'
const RELOAD_TABLE = 'RELOAD_ANALYSES_TABLE'

const TOGGLE_SIDEBAR = 'TOGGLE_SIDEBAR'

/** -------------ACTIONS------------------------ **/

const getRecentAnalysis = thunkAction(GET_RECENT_ANALYSIS, ({token, fulfilled}) => {
  return getSavedAnalysesApi(token, {sort: 'id'}).then(fulfilled)
})

const confirmDeleteAnalyses = createAction(CONFIRM_DELETE_ANALYSES, (idsArray) => idsArray)
const cancelDeleteAnalyses = createAction(CANCEL_DELETE_ANALYSES)
const deleteSavedAnalysesPending = () => {
  return {type: DELETE_ANALYSES + '_PENDING'}
}

const deleteSavedAnalysesFulfilled = (payload) => {
  return {type: DELETE_ANALYSES + '_FULFILLED', payload}
}

const deleteSavedAnalyses = () => {
  return tokenInject((dispatch, getState, token) => {
    dispatch(deleteSavedAnalysesPending())
    const tableState = getState().getIn(['appState', 'analyzeTab', 'tableState']).toJS()
    deleteSavedAnalysesApi(
      token, tableState.idsToDelete
    ).then(() => {
      return getSavedAnalysesApi(token, {
        page: tableState.currentPage,
        limit: tableState.limitByPage,
        sort: tableState.sortByField,
        direction: tableState.sortDirection
      })
    }).then((data) => {
      dispatch(deleteSavedAnalysesFulfilled(data))
    })
  })
}

/**
Change table page or sort state
params = {currentPage, limitByPage, sortField, sortDirection}
*/
const setAnalysesTableParams = createAction(SET_TABLE_PARAMS, (params) => params)
const selectAnalysesTableRow = createAction(SELECT_TABLE_ROW, (itemId) => itemId)
const selectAnalysesTableAllRows = createAction(SELECT_ALL_ROWS)

const reloadAnalysesTablePending = () => {
  return {type: RELOAD_TABLE + '_PENDING'}
}

const reloadAnalysesTableFulfilled = (payload) => {
  return {type: RELOAD_TABLE + '_FULFILLED', payload}
}

const reloadAnalysesTable = (params) => {
  return tokenInject((dispatch, getState, token) => {
    dispatch(reloadAnalysesTablePending())
    dispatch(setAnalysesTableParams(params))
    const state = getState().getIn(['appState', 'analyzeTab', 'tableState']).toJS()
    getSavedAnalysesApi(token, {
      page: state.currentPage,
      limit: state.limitByPage,
      sort: state.sortByField,
      direction: state.sortDirection
    }).then((data) => {
      dispatch(reloadAnalysesTableFulfilled(data))
    })
  })
}

export const actions = {
  getRecentAnalysis,
  confirmDeleteAnalyses,
  cancelDeleteAnalyses,
  deleteSavedAnalyses,
  setAnalysesTableParams,
  selectAnalysesTableAllRows,
  selectAnalysesTableRow,
  reloadAnalysesTable
}

/** -----------STATE--------- **/

export const initialState = fromJS({
  //for welcome tab
  recentAnalysis: [],
  isRecentAnalysisPending: false,
  //for saved tab
  isSidebarVisible: true,
  sidebarState: {
    isRecentlyViewedPending: true,
    recentlyViewed: []
  },
  tableState: {
    currentPage: 1,
    limitByPage: 10,
    sortByField: 'id',
    sortDirection: 'asc',
    data: [],
    count: 0,
    totalCount: 0,
    isLoading: true,
    isDeletePopupVisible: false,
    idsToDelete: [],
    selectedIds: {},  //map of ids of items that selected in table
    isAllSelected: false
  }
})

/** -----------HANDLERS--------------- **/

export default handleActions({
  [`${GET_RECENT_ANALYSIS}_PENDING`]: (state) => {
    return state.merge({
      recentAnalysis: [],
      isRecentAnalysisPending: true
    })
  },

  [`${GET_RECENT_ANALYSIS}_FULFILLED`]: (state, {payload}) => {
    return state.merge({
      recentAnalysis: payload.data,
      isRecentAnalysisPending: false
    })
  },

  [CONFIRM_DELETE_ANALYSES]: (state, {payload: idsArray}) => {
    return state.mergeIn(['tableState'], {
      isDeletePopupVisible: true,
      idsToDelete: idsArray
    })
  },

  [CANCEL_DELETE_ANALYSES]: (state) => {
    return state.mergeIn(['tableState'], {
      isDeletePopupVisible: false,
      idsToDelete: []
    })
  },

  [`${DELETE_ANALYSES}_PENDING`]: (state) => {
    return state.mergeIn(['tableState'], {
      isDeletePopupVisible: false,
      isLoading: true
    })
  },

  [`${DELETE_ANALYSES}_FULFILLED`]: (state, {payload}) => {
    return state.mergeIn(['tableState'], {
      data: payload.data,
      count: payload.count,
      totalCount: payload.totalCount,
      isLoading: false
    })
  },

  [SET_TABLE_PARAMS]: (state, {payload: tableState}) => {
    return state.mergeIn(['tableState'], tableState)
  },

  [SELECT_TABLE_ROW]: (state, {payload: {itemId, select}}) => {
    const path = ['tableState', 'selectedIds', itemId.toString()]
    return select ? state.setIn(path, 1) : state.deleteIn(path)
  },

  [SELECT_ALL_ROWS]: (state) => {
    const isAllSelected = state.getIn(['tableState', 'isAllSelected'])

    if (isAllSelected) { //then deselect all
      return state.mergeIn(['tableState'], {
        isAllSelected: false,
        selectedIds: {}
      })
    } else { //select all currently loaded data
      let selectedIds = {}
      const data = state.getIn(['tableState', 'data']).toJS()
      data.forEach((item) => { selectedIds[item.id.toString()] = 1 })
      return state.mergeIn(['tableState'], {
        isAllSelected: true,
        selectedIds: selectedIds
      })
    }
  },

  [`${RELOAD_TABLE}_PENDING`]: (state, {payload: params}) => {
    return state.setIn(['tableState', 'selectedIds'], Map({})).setIn(['tableState', 'isLoading'], true)
  },

  [`${RELOAD_TABLE}_FULFILLED`]: (state, {payload}) => {
    return state.mergeIn(['tableState'], {
      data: payload.data,
      count: payload.count,
      totalCount: payload.totalCount,
      isLoading: false
    })
  },

  [TOGGLE_SIDEBAR]: (state, {payload}) => {
    const isVisible = !state.get('isSidebarVisible')
    return state.set('isSidebarVisible', isVisible)
  }

}, initialState)
