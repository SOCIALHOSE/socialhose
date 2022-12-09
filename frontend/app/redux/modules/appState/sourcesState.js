import { createAction, handleActions } from 'redux-actions'
import { fromJS } from 'immutable'
import {thunkAction} from '../../utils/common'
import * as api from '../../../api/searchApi'
import { addAlert } from '../common/alerts'
import {filtersFromServerFormat, ADV_FILTERS_LIMIT} from '../../utils/helpers/advancedFilters'

/*
 * Constants
 * */
export const GET_SOURCE_INDEXES = 'GET_SOURCE_INDEXES'

export const SET_SOURCE_INDEX_SEARCH_QUERY = 'SET_SOURCE_INDEX_SEARCH_QUERY'

export const TOGGLE_SOURCE_INDEX = 'TOGGLE_SOURCE_INDEX'
export const TOGGLE_ALL_SOURCE_INDEXES = 'TOGGLE_ALL_SOURCE_INDEXES'

export const GET_MAIN_SOURCE_LISTS = 'GET_MAIN_SOURCE_LISTS'
export const TOGGLE_ONLY_GLOBAL = 'TOGGLE_ONLY_GLOBAL'

export const TOGGLE_ADD_SOURCE_TO_LIST_POPUP = 'TOGGLE_ADD_SOURCE_TO_LIST_POPUP'
export const ADD_SOURCE_LIST = 'ADD_SOURCE_LIST'
export const DELETE_SOURCE_LIST = 'DELETE_SOURCE_LIST'
export const RENAME_SOURCE_LIST = 'RENAME_SOURCE_LIST'
export const CLONE_SOURCE_LIST = 'CLONE_SOURCE_LIST'
export const ADD_SOURCES_TO_LIST = 'ADD_SOURCES_TO_LIST'
export const UPDATE_LIST_SOURCES = 'UPDATE_LIST_SOURCES'
export const SET_CHOSEN_LISTS_TO_ADD_SOURCES = 'SET_CHOSEN_LISTS_TO_ADD_SOURCES'

export const SHOW_UPDATE_SOURCE_POPUP = 'SHOW_UPDATE_SOURCE_POPUP'
export const HIDE_UPDATE_SOURCE_POPUP = 'HIDE_UPDATE_SOURCE_POPUP'
export const SET_CHOSEN_LISTS_TO_UPDATE_SOURCES = 'SET_CHOSEN_LISTS_TO_UPDATE_SOURCES'

export const TOGGLE_ADD_LIST_POPUP = 'TOGGLE_ADD_LIST_POPUP'

export const TOGGLE_DELETE_LIST_POPUP = 'TOGGLE_DELETE_LIST_POPUP'

export const TOGGLE_RENAME_LIST_POPUP = 'TOGGLE_RENAME_LIST_POPUP'

export const TOGGLE_CLONE_LIST_POPUP = 'TOGGLE_CLONE_LIST_POPUP'

export const TOGGLE_SOURCE_INFO_POPUP = 'TOGGLE_SOURCE_INFO_POPUP'

export const GET_SOURCES_OF_LIST = 'GET_SOURCES_OF_LIST'
export const SHOW_SOURCES_OF_LIST = 'SHOW_SOURCES_OF_LIST'
export const HIDE_SOURCES_OF_LIST = 'HIDE_SOURCES_OF_LIST'
export const SET_SOURCES_OF_LIST_SEARCH_QUERY = 'SET_SOURCES_OF_LIST_SEARCH_QUERY'

const SELECT_SOURCES_FILTER = 'SELECT_SOURCES_FILTER'
const CLEAR_SOURCES_FILTERS = 'CLEAR_SOURCES_FILTERS'
const CLEAR_ALL_SOURCES_FILTERS = 'CLEAR_ALL_SOURCES_FILTERS'
const LOAD_MORE_SOURCES_FILTERS = 'LOAD_MORE_SOURCES_FILTERS'
const LOAD_LESS_SOURCES_FILTERS = 'LOAD_LESS_SOURCES_FILTERS'

const SHARE_SOURCE_LIST = 'SHARE_SOURCE_LIST'
const UNSHARE_SOURCE_LIST = 'UNSHARE_SOURCE_LIST'

/*
 * Actions
 * */
const getSourceIndexes = thunkAction(GET_SOURCE_INDEXES, (params, {token, getState, fulfilled}) => {
  const sourceIndexesState = getState().getIn(['appState', 'sourcesState', 'sourceIndexesState'])
  const query = sourceIndexesState.get('searchQuery')
  const page = sourceIndexesState.get('page')
  const limit = sourceIndexesState.get('limit')
  const selectedFilters = sourceIndexesState.getIn(['advancedFilters', 'selected'])
  let dataToSend = {
    query,
    page,
    limit
  }
  if (Object.keys(selectedFilters).length > 0) {
    dataToSend.advancedFilters = selectedFilters
  }
  if (params) {
    Object.assign(dataToSend, params)
  }

  return api.searchSources(token, dataToSend)
    .then((response) => fulfilled(response))
}, true)

export const setSourceIndexSearchQuery = createAction(SET_SOURCE_INDEX_SEARCH_QUERY, (query) => query)

export const toggleSourceIndex = createAction(TOGGLE_SOURCE_INDEX, itemId => itemId)
export const toggleAllSourceIndexes = createAction(TOGGLE_ALL_SOURCE_INDEXES, isChosen => isChosen)

export const getMainSourceLists = thunkAction(GET_MAIN_SOURCE_LISTS, (dataToSend, {token, fulfilled}) => {
  return api.getSourceLists(token, dataToSend)
    .then((response) => fulfilled(response))
}, true)

export const toggleOnlyGlobal = createAction(TOGGLE_ONLY_GLOBAL)

export const toggleAddSourceToListPopup = createAction(TOGGLE_ADD_SOURCE_TO_LIST_POPUP)

export const setChosenListsToAddSources = createAction(SET_CHOSEN_LISTS_TO_ADD_SOURCES, (newLists) => newLists)

const addSourcesToList = thunkAction(ADD_SOURCES_TO_LIST, (dataToSend, isAdd, {token, dispatch, getState}) => {
  return api.addSourcesToLists(token, dataToSend)
    .then(() => {
      dispatch(getSourceIndexes(null))
      dispatch(addAlert({
        type: 'notice',
        transKey: 'updateListsForSourceNotice',
        id: 'updateListsForSourceNotice'
      }))
      isAdd 
      ? dispatch(toggleAddSourceToListPopup())
      : dispatch(hideUpdateSourcePopup())
    })
})

const addSourceList = thunkAction(ADD_SOURCE_LIST, (name, {token, dispatch}) => {
  return api.addSourceLists(token, name)
    .then(() => {
      dispatch(getMainSourceLists({}))
      dispatch(addAlert({
        type: 'notice',
        transKey: 'addSourceList',
        parameters: {
          name
        }
      }))
      dispatch(toggleAddListPopup())
    })
})

const deleteSourceList = thunkAction(DELETE_SOURCE_LIST, (data, {token, dispatch}) => {
  return api.deleteSourceLists(token, data.id)
    .then(() => {
      dispatch(getMainSourceLists({}))

      dispatch(toggleDeleteListPopup())

      dispatch(addAlert({
        type: 'notice',
        transKey: 'deleteSourceList',
        parameters: {name: data.name}
      }))

    })
})

const renameSourceList = thunkAction(RENAME_SOURCE_LIST, (data, oldName, {token, dispatch}) => {
  return api.renameSourceLists(token, data)
    .then(() => {
      dispatch(getMainSourceLists({}))
      dispatch(addAlert({
        type: 'notice',
        transKey: 'renameSourceList',
        parameters: oldName
      }))
      dispatch(toggleRenameListPopup())
    })
})

const cloneSourceList = thunkAction(CLONE_SOURCE_LIST, (data, {token, dispatch}) => {
  return api.cloneSourceLists(token, data)
    .then(() => {
      dispatch(getMainSourceLists({}))
      dispatch(addAlert({
        type: 'notice',
        transKey: 'cloneSourceList'
      }))
      dispatch(toggleCloneListPopup())
    })
})

const updateListSources = thunkAction(UPDATE_LIST_SOURCES, (dataToSend, {token, dispatch, getState}) => {
  return api.replaceSourceListsForSource(token, dataToSend)
    .then(() => {
      dispatch(addAlert({
        type: 'notice',
        transKey: 'updateListsForSourceNotice',
        id: 'updateListsForSourceNotice'
      }))

      const sourcesOfListState = getState().getIn(['appState', 'sourcesState', 'sourcesOfListState'])
      const query = sourcesOfListState.get('searchQuery')
      const page = sourcesOfListState.get('page')
      const limit = sourcesOfListState.get('limit')
      const listId = sourcesOfListState.getIn(['visibleList', 'id'])
      dispatch(getSourcesOfList(listId, {query, page, limit}))
    })
})

const shareSourceList = thunkAction(SHARE_SOURCE_LIST, (id, {token, dispatch}) => {
  return api.shareSourceList(token, id)
    .then(() => {
      dispatch(getMainSourceLists({}))
      dispatch(addAlert({
        type: 'notice',
        transKey: 'shareSourceList'
      }))
    })
})

const unshareSourceList = thunkAction(UNSHARE_SOURCE_LIST, (id, {token, dispatch}) => {
  return api.unshareSourceList(token, id)
    .then(() => {
      dispatch(getMainSourceLists({}))
      dispatch(addAlert({
        type: 'notice',
        transKey: 'unshareSourceList'
      }))
    })
})

export const showUpdateSourcePopup = createAction(SHOW_UPDATE_SOURCE_POPUP, (chosenSource) => chosenSource)
export const hideUpdateSourcePopup = createAction(HIDE_UPDATE_SOURCE_POPUP)
export const setChosenListsToUpdateSources = createAction(SET_CHOSEN_LISTS_TO_UPDATE_SOURCES, (newLists) => newLists)

export const toggleAddListPopup = createAction(TOGGLE_ADD_LIST_POPUP)

export const _toggleDeletePopup = createAction(TOGGLE_DELETE_LIST_POPUP, (type, list) => ({type, list}))

export const toggleDeleteListPopup = (list) => (dispatch) => {
  dispatch(_toggleDeletePopup('sourceListsState', list))
}
export const toggleDeleteListIndexPopup = (list) => (dispatch) => {
  dispatch(_toggleDeletePopup('sourcesOfListState', list))
}

export const toggleRenameListPopup = createAction(TOGGLE_RENAME_LIST_POPUP, (list) => list)

export const toggleCloneListPopup = createAction(TOGGLE_CLONE_LIST_POPUP, (list) => list)

const toggleInfoSourcePopup = createAction(TOGGLE_SOURCE_INFO_POPUP, (type, item) => ({type, item}))

export const getSourcesOfList = thunkAction(GET_SOURCES_OF_LIST, (id, dataToSend, {token, fulfilled}) => {
  return api
    .getSourcesOfList(token, dataToSend, id)
    .then((response) => fulfilled(response))
}, true)

export const showSourcesOfList = createAction(SHOW_SOURCES_OF_LIST, (list) => list)

export const hideSourcesOfList = createAction(HIDE_SOURCES_OF_LIST)

export const setSourcesOfListSearchQuery = createAction(SET_SOURCES_OF_LIST_SEARCH_QUERY, (query) => query)

export const selectSourcesFilter = createAction(SELECT_SOURCES_FILTER, (groupName, filterValue) => { return {groupName, filterValue} })
export const clearSourcesFilters = createAction(CLEAR_SOURCES_FILTERS)
export const clearAllSourcesFilters = createAction(CLEAR_ALL_SOURCES_FILTERS)
export const loadMoreSourcesFilters = createAction(LOAD_MORE_SOURCES_FILTERS)
export const loadLessSourcesFilters = createAction(LOAD_LESS_SOURCES_FILTERS)

export const actions = {
  getSourceIndexes,
  setSourceIndexSearchQuery,
  toggleSourceIndex,
  toggleAllSourceIndexes,
  getMainSourceLists,
  toggleOnlyGlobal,
  toggleAddSourceToListPopup,
  addSourcesToList,
  updateListSources,
  setChosenListsToAddSources,
  showUpdateSourcePopup,
  hideUpdateSourcePopup,
  setChosenListsToUpdateSources,
  toggleInfoSourcePopup,
  toggleAddListPopup,
  toggleDeleteListPopup,
  toggleDeleteListIndexPopup,
  toggleRenameListPopup,
  toggleCloneListPopup,
  getSourcesOfList,
  showSourcesOfList,
  hideSourcesOfList,
  setSourcesOfListSearchQuery,
  selectSourcesFilter,
  clearSourcesFilters,
  clearAllSourcesFilters,
  loadMoreSourcesFilters,
  loadLessSourcesFilters,
  addSourceList,
  deleteSourceList,
  renameSourceList,
  cloneSourceList,
  shareSourceList,
  unshareSourceList
}

/*
 * State
 * */
export const initialState = fromJS({
  sourceIndexesState: {
    searchQuery: '',
    page: 1,
    limit: 25,
    sortByField: 'id',
    sortDirection: 'asc',
    data: [],
    count: 0,
    totalCount: 0,
    isLoading: false,
    isAddPopupVisible: false,
    isUpdatePopupVisible: false,
    infoPopup: {
      visible: false,
      item: null
    },
    chosenListsToAddSources: [],
    chosenSourceToUpdate: {}, // source id on which we click to add / remove it.
    idsToDelete: [],
    selectedIds: [],  //map of ids of items that selected in table
    isAllSelected: false,
    advancedFilters: {
      all: {},
      pages: {}, //{groupName1: {count: xx, totalCount: yy}, groupName2: ....}
      selected: {}, // {groupName1: {value1: 0, value2: 1, ....}, groupName2: {}, ... } will be send to the server
      pending: {} //which groups is not applied yet
    }
  },
  sourceListsState: {
    page: 1,
    limit: 25,
    sortByField: 'id',
    sortDirection: 'asc',
    data: [],
    count: 0,
    totalCount: 0,
    onlyGlobal: false,
    isLoading: false,
    isAddListPopupVisible: false,
    isDeletePopupVisible: false,
    isRenameListPopupVisible: false,
    isCloneListPopupVisible: false,
    listToEdit: {}
  },
  sourcesOfListState: {
    isSourcesOfListVisible: false,
    visibleList: null,
    searchQuery: '',
    page: 1,
    limit: 25,
    sortByField: 'id',
    sortDirection: 'asc',
    data: [],
    count: 0,
    totalCount: 0,
    infoPopup: {
      visible: false,
      item: null
    },
    isDeletePopupVisible: false,
    isLoading: false,
    listToEdit: {}
  }
})

/*
 * Reducers
 * */
export default handleActions({

  [`${GET_SOURCE_INDEXES} pending`]: (state, { payload }) => {
    return state.setIn(['sourceIndexesState', 'isLoading'], payload.isPending)
  },

  [`${GET_SOURCE_INDEXES} fulfilled`]: (state, { payload: {sources, advancedFilters, meta} }) => {

    const {allFilters, pages} = filtersFromServerFormat(advancedFilters)

    return state.mergeIn(['sourceIndexesState'], {
      'data': sources.data,
      'isLoading': false,
      'page': sources.page,
      'limit': sources.limit,
      'count': sources.count,
      'totalCount': sources.totalCount,
      'sortByField': meta.sort.field || 'name',
      'sortDirection': meta.sort.direction || 'asc'
    }).mergeIn(['sourceIndexesState', 'advancedFilters'], {
      all: allFilters,
      pages: pages,
      selected: meta.advancedFilters,
      pending: {}
    })
  },

  [SET_SOURCE_INDEX_SEARCH_QUERY]: (state, {payload: query}) => {
    return state.setIn(['sourceIndexesState', 'searchQuery'], query)
  },

  [TOGGLE_SOURCE_INDEX]: (state, {payload: itemId}) => {
    const path = ['sourceIndexesState', 'selectedIds']

    let selectedIds = state.getIn(path)
    const isSelected = selectedIds.includes(itemId)
    if (isSelected) {
      selectedIds = selectedIds.filter(id => id !== itemId)
    }
    else {
      selectedIds = selectedIds.push(itemId)
    }
    return state.setIn(path, selectedIds)
  },

  [TOGGLE_ALL_SOURCE_INDEXES]: (state) => {
    const type = 'sourceIndexesState'
    const isAllSelected = state.getIn([type, 'isAllSelected'])
    if (isAllSelected) { //then deselect all
      return state.mergeIn([type], {
        isAllSelected: false,
        selectedIds: []
      })
    }
    else { //select all currently loaded data
      const selectedIds = state.getIn([type, 'data']).map(item => item.get('id'))
      return state.mergeIn([type], {
        isAllSelected: true,
        selectedIds
      })
    }
  },

  [`${GET_MAIN_SOURCE_LISTS} pending`]: (state, { payload }) => {
    return state.setIn(['sourceListsState', 'isLoading'], payload.isPending)
  },

  [`${GET_MAIN_SOURCE_LISTS} fulfilled`]: (state, { payload }) => {
    const response = payload.data
    return state.mergeIn(['sourceListsState'], {
      'data': response,
      'isLoading': false,
      'page': payload.page,
      'limit': payload.limit,
      'count': payload.count,
      'totalCount': payload.totalCount,
      'sortByField': payload.sort.field || 'name',
      'sortDirection': payload.sort.direction || 'asc'
    })
  },

  [TOGGLE_ONLY_GLOBAL]: (state) => {
    const onlyGlobal = state.getIn(['sourceListsState', 'onlyGlobal'])
    return state.setIn(['sourceListsState', 'onlyGlobal'], !onlyGlobal)
  },

  [TOGGLE_ADD_SOURCE_TO_LIST_POPUP]: (state, { payload }) => {
    const isVisible = !state.getIn(['sourceIndexesState', 'isAddPopupVisible'])
    return state.mergeIn(['sourceIndexesState'], {
      'isAddPopupVisible': isVisible,
      'chosenListsToAddSources': []
    })
  },

  [SET_CHOSEN_LISTS_TO_ADD_SOURCES]: (state, {payload: newSources}) => {
    return state.setIn(['sourceIndexesState', 'chosenListsToAddSources'], newSources)
  },

  [SHOW_UPDATE_SOURCE_POPUP]: (state, { payload: chosenSource }) => {
    return state.mergeIn(['sourceIndexesState'], {
      'isUpdatePopupVisible': true,
      'chosenSourceToUpdate': chosenSource
    })
  },

  [HIDE_UPDATE_SOURCE_POPUP]: (state, { payload }) => {
    return state.mergeIn(['sourceIndexesState'], {
      'isUpdatePopupVisible': false,
      'chosenSourceToUpdate': {}
    })
  },

  [SET_CHOSEN_LISTS_TO_UPDATE_SOURCES]: (state, {payload: newSources}) => {
    return state.setIn(['sourceIndexesState', 'chosenSourceToUpdate', 'listIds'], newSources)
  },

  [TOGGLE_ADD_LIST_POPUP]: (state, {payload}) => {
    const isVisible = !state.getIn(['sourceListsState', 'isAddListPopupVisible'])
    return state.setIn(['sourceListsState', 'isAddListPopupVisible'], isVisible)
  },

  [TOGGLE_DELETE_LIST_POPUP]: (state, {payload}) => {
    const { type, list } = payload
    const isVisible = !state.getIn([type, 'isDeletePopupVisible'])
    return state.mergeIn([type], {
      'isDeletePopupVisible': isVisible,
      'listToEdit': isVisible ? list : {}
    })
  },

  [TOGGLE_RENAME_LIST_POPUP]: (state, {payload: list}) => {
    const isVisible = !state.getIn(['sourceListsState', 'isRenameListPopupVisible'])
    return state.mergeIn(['sourceListsState'], {
      'isRenameListPopupVisible': isVisible,
      'listToEdit': isVisible ? list : {}
    })
  },

  [TOGGLE_CLONE_LIST_POPUP]: (state, {payload: list}) => {
    const isVisible = !state.getIn(['sourceListsState', 'isCloneListPopupVisible'])
    return state.mergeIn(['sourceListsState'], {
      'isCloneListPopupVisible': isVisible,
      'listToEdit': isVisible ? list : {}
    })
  },

  [TOGGLE_SOURCE_INFO_POPUP]: (state, {payload}) => {
    const { type, item } = payload
    const popupPath = [type, 'infoPopup']
    const isVisible = !state.getIn(popupPath.concat('visible'))
    return state.mergeIn(popupPath, {
      visible: isVisible,
      item: isVisible ? item : null
    })
  },

  [`${GET_SOURCES_OF_LIST} pending`]: (state, { payload }) => {
    return state.setIn(['sourcesOfListState', 'isLoading'], payload.isPending)
  },

  [`${GET_SOURCES_OF_LIST} fulfilled`]: (state, { payload }) => {
    const sources = payload.sources
    return state.mergeIn(['sourcesOfListState'], {
      'data': sources.data,
      'isLoading': false,
      'page': sources.page,
      'limit': sources.limit,
      'count': sources.count,
      'totalCount': sources.totalCount,
      'sortByField': payload.sort.field || 'name',
      'sortDirection': payload.sort.direction || 'asc'
    })
  },

  [SHOW_SOURCES_OF_LIST]: (state, {payload: list}) => {
    return state.mergeIn(['sourcesOfListState'], {
      'isSourcesOfListVisible': true,
      'visibleList': list
    })
  },

  [HIDE_SOURCES_OF_LIST]: (state, {payload}) => {
    return state.mergeIn(['sourcesOfListState'], {
      'isSourcesOfListVisible': false,
      'visibleList': {},
      'data': []
    })
  },

  [SET_SOURCES_OF_LIST_SEARCH_QUERY]: (state, {payload: query}) => {
    return state.setIn(['sourcesOfListState', 'searchQuery'], query)
  },

  [SELECT_SOURCES_FILTER]: (state, {payload: {groupName, filterValue}}) => {
    const basePath = ['sourceIndexesState', 'advancedFilters']
    const selectionPath = [...basePath, 'selected', groupName]
    if (groupName === 'articleDate') {
      state = state.deleteIn(selectionPath)
    }
    //tri-state switch
    const currentState = state.getIn([...selectionPath, filterValue])
    let newState
    if (currentState === undefined) {
      newState = 1
    } else if (currentState === 1) {
      newState = -1
    }
    return state.deleteIn([...basePath, 'pending', groupName]).setIn([...selectionPath, filterValue], newState)
  },

  [CLEAR_SOURCES_FILTERS]: (state, {payload: groupName}) => {
    const basePath = ['sourceIndexesState', 'advancedFilters']
    return state.setIn([...basePath, 'pending', groupName], true).deleteIn([...basePath, 'selected', groupName])
  },

  [CLEAR_ALL_SOURCES_FILTERS]: (state) => {
    return state.mergeIn(['sourceIndexesState', 'advancedFilters'], {
      selected: {},
      pending: {}
    })
  },

  [LOAD_LESS_SOURCES_FILTERS]: (state, {payload: groupName}) => {
    const path = ['sourceIndexesState', 'advancedFilters', 'pages', groupName, 'count']
    const currentCount = state.getIn(path)
    return state.setIn(path, Math.max(currentCount - ADV_FILTERS_LIMIT, ADV_FILTERS_LIMIT))
  },

  [LOAD_MORE_SOURCES_FILTERS]: (state, {payload: groupName}) => {
    const path = ['sourceIndexesState', 'advancedFilters', 'pages', groupName]
    const currentCount = state.getIn([...path, 'count'])
    console.log(currentCount)
    const totalCount = state.getIn([...path, 'totalCount'])
    return state.setIn([...path, 'count'], Math.min(currentCount + ADV_FILTERS_LIMIT, totalCount))
  }

}, initialState)
