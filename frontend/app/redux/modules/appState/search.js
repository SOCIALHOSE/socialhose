import {createAction, handleActions} from 'redux-actions'
import {fromJS} from 'immutable'
import * as searchApi from '../../../api/searchApi'
import * as feedsApi from '../../../api/feedsApi'
import {addAlert} from '../common/alerts'
import {getRestrictions} from '../common/auth'
import {getSidebarCategories} from './sidebar'
import {renewSearchBy, setCommonFilters} from './searchByFilters'
import {thunkAction, tokenInject} from '../../utils/common'
import * as helpers from '../../utils/helpers/search'
import { findFeedById } from '../../utils/helpers/sidebar'
import {filtersFromServerFormat, ADV_FILTERS_LIMIT} from '../../utils/helpers/advancedFilters'

import {
  COMMENT_ARTICLE,
  DELETE_ARTICLES,
  DELETE_ARTICLES_FROM_FEED,
  DELETE_COMMENT,
  LOAD_MORE_COMMENTS,
  UPDATE_COMMENT
} from './articles'

/*
 * Constants
 * */
const GET_SEARCH_RESULTS = 'GET_SEARCH_RESULTS'

const TOGGLE_REFINE_PANEL = 'TOGGLE_REFINE_PANEL'
const SELECT_REFINE_FILTER = 'SELECT_REFINE_FILTER'
const CLEAR_REFINE_FILTERS = 'CLEAR_REFINE_FILTERS'
const CLEAR_ALL_REFINE_FILTERS = 'CLEAR_ALL_REFINE_FILTERS'
const LOAD_MORE_REFINE_FILTERS = 'LOAD_MORE_REFINE_FILTERS'
const LOAD_LESS_REFINE_FILTERS = 'LOAD_LESS_REFINE_FILTERS'

const TOGGLE_SAVE_FEED_POPUP = 'TOGGLE_SAVE_FEED_POPUP'

const SET_FEED_RESULTS = 'SET_FEED_RESULTS'

const EDIT_FEED = 'EDIT_FEED'

const SET_NEW_SEARCH = 'SET_NEW_SEARCH'
const CHANGE_FEED_QUERY = 'CHANGE_FEED_QUERY'
const SET_ACTIVE_FEED = 'SET_ACTIVE_FEED'
const CHANGE_ACTIVE_FEED_NAME = 'CHANGE_ACTIVE_FEED_NAME'

const SEARCH_SET_VALUE = 'SEARCH_SET_VALUE'

const SELECT_ARTICLE = 'SELECT_ARTICLE'
const SELECT_ALL_ARTICLES = 'SELECT_ALL_ARTICLES'
const SAVE_FEED = 'SAVE_FEED'
const SAVE_AS_FEED = 'SAVE_AS_FEED'

/*
 * Actions
 * */
const getSearchResultsPending = createAction(GET_SEARCH_RESULTS + '_PENDING')
const getSearchResultsRejected = createAction(GET_SEARCH_RESULTS + '_REJECTED', (errors) => errors)
const setFeedResults = createAction(SET_FEED_RESULTS, (response) => response)

const _getSearchResults = (dispatch, getState, apiPromise, initialSearch = false) => {
  dispatch(getSearchResultsPending())
  return apiPromise
    .then((response) => {
      dispatch(setFeedResults(response))
      if (response.meta.search.filters) {
        dispatch(setCommonFilters(response.meta.search.filters, response.meta.sourceLists, response.meta.sources))
      } else {
        dispatch(renewSearchBy())
      }
      if (response.feed) {
        const categories = getState().getIn(['appState', 'sidebar', 'categories']).toJS()
        const feed = findFeedById(categories, parseInt(response.feed))
        dispatch(setActiveFeed(fromJS(feed)))
      }
      const isFreeUser = getState().getIn(['common', 'auth', 'user', 'restrictions', 'plans', 'price']) === 0;
      if (initialSearch && isFreeUser) {
        const email = getState().getIn(['common', 'auth', 'user', 'email'])
        searchApi.submitSearchHubspot({
          email: email,
          searchquery: response.meta.search.query
        });
      }
      dispatch(getRestrictions())
    })
    .catch((errors) => {
      dispatch(getSearchResultsRejected(errors))
      dispatch(addAlert(errors))
    })
}

const getSearchResults = (data, initialSearch) => {
  return tokenInject((dispatch, getState, token) => {
    const apiPromise = searchApi.searchQuery(token, data)
    _getSearchResults(dispatch, getState, apiPromise, initialSearch)
  })
}

const getFeedResults = (data, feedId) => {
  return tokenInject((dispatch, getState, token) => {
    const apiPromise = feedsApi.getFeedSearchResults(token, data, feedId)
    _getSearchResults(dispatch, getState, apiPromise)
  })
}

const saveAsFeed = thunkAction(SAVE_AS_FEED, (dataToSend, {token, dispatch, fulfilled}) => {
  return feedsApi
    .createFeed(token, dataToSend)
    .then(() => {
      fulfilled()
      dispatch(getSidebarCategories())
      dispatch(getRestrictions())
      dispatch(addAlert({
        type: 'notice',
        transKey: 'saveFeed',
        id: 'saveFeed'
      }))
    })
}, true)

const saveFeed = thunkAction(SAVE_FEED, (dataToSend, feedId, {token, dispatch, fulfilled}) => {
  return feedsApi
    .saveFeed(token, dataToSend, feedId)
    .then(() => {
      fulfilled()
      dispatch(getSidebarCategories())
      dispatch(getRestrictions())
      dispatch(addAlert({
        type: 'notice',
        transKey: 'saveFeed',
        id: 'saveFeed'
      }))
    })
}, true)

const toggleRefinePanel = createAction(TOGGLE_REFINE_PANEL)
const selectRefineFilter = createAction(SELECT_REFINE_FILTER, (groupName, filterValue) => {
  return {groupName, filterValue}
})
const clearRefineFilters = createAction(CLEAR_REFINE_FILTERS)
const clearAllRefineFilters = createAction(CLEAR_ALL_REFINE_FILTERS)
const loadMoreRefineFilters = createAction(LOAD_MORE_REFINE_FILTERS, groupName => groupName)
const loadLessRefineFilters = createAction(LOAD_LESS_REFINE_FILTERS, groupName => groupName)

const toggleSaveFeedPopup = createAction(TOGGLE_SAVE_FEED_POPUP)

const editFeed = createAction(EDIT_FEED)
const setNewSearch = createAction(SET_NEW_SEARCH)
const changeFeedQuery = createAction(CHANGE_FEED_QUERY, value => value)

const setActiveFeed = createAction(SET_ACTIVE_FEED, feed => feed)

const changeActiveFeedName = createAction(CHANGE_ACTIVE_FEED_NAME, feedName => feedName)

const selectArticle = createAction(SELECT_ARTICLE, article => article)
const selectAllArticles = createAction(SELECT_ALL_ARTICLES, select => select)

export const actions = {
  getSearchResults,
  toggleSaveFeedPopup,
  toggleRefinePanel,
  loadMoreRefineFilters,
  loadLessRefineFilters,
  selectRefineFilter,
  clearRefineFilters,
  clearAllRefineFilters,
  setFeedResults,
  editFeed,
  setNewSearch,
  saveAsFeed,
  saveFeed,
  getFeedResults,
  setActiveFeed,
  changeActiveFeedName,
  changeFeedQuery,
  selectArticle,
  selectAllArticles
}

/*
 * State
 * */
export const initialState = fromJS({
  isEditingFeed: false,
  isSavingFeed: false,
  activeFeed: null,
  loadedFeedName: null,
  loadedFeedQuery: null,
  searchResults: [],
  searchResultsErrors: [],
  searchResultsPending: false,
  searchResultCount: 0,
  searchResultTotalCount: 0,
  searchResultPage: 1,
  searchResultLimit: 100,
  isSavedSearchVisible: false,
  isSaveFeedPopupVisible: false,
  isSynced: true,
  isLoaded: false,
  advancedFilters: {
    all: {},
    pages: {}, //{groupName1: {count: xx, totalCount: yy}, groupName2: ....}
    selected: {}, // {keyword: {"fsdfdsf": 1}, groupName1: {value1: 0, value2: 1, ....}, groupName2: {}, ... } will be sent to the server
    pending: {}, //which groups is not applied yet
    isVisible: true
  },
  selectedArticles: []
})

const deselectArticlesReducer = (state, {payload: ids}) => {
  const selectedArticles = state.get('selectedArticles').toJS()
  const filtered = selectedArticles.filter((article) => !ids.includes(article.id))
  return state.set('selectedArticles', fromJS(filtered))
}
/*
 * Reducers
 * */
export default handleActions({

  [SEARCH_SET_VALUE]: (state, {payload}) => {
    const {field, value} = payload
    return state.set(field, value)
  },

  [`${GET_SEARCH_RESULTS}_PENDING`]: (state) => {
    return state.merge({
      searchResults: [],
      searchResultCount: 0,
      searchResultTotalCount: 0,
      searchResultPage: 1,
      searchResultsPending: true
    })
  },

  [`${GET_SEARCH_RESULTS}_REJECTED`]: (state, {payload}) => {
    return state.merge({
      searchResults: [],
      searchResultsErrors: payload,
      searchResultCount: 0,
      searchResultTotalCount: 0,
      searchResultsPending: false
    })
  },

  [SET_FEED_RESULTS]: (state, {payload: response}) => {
    const {documents, advancedFilters, meta} = response

    const selectedFilters = meta.search.advancedFilters
    const {allFilters, pages} = filtersFromServerFormat(advancedFilters)

    delete allFilters.reach // to hide reach in advanced filters

    helpers.mergeAdvancedFilters(allFilters, selectedFilters, pages)

    return state
      .merge({
        searchResults: documents.data,
        searchResultsErrors: [],
        searchResultCount: documents.count,
        searchResultTotalCount: documents.totalCount,
        searchResultPage: documents.page,
        searchResultLimit: documents.limit,
        searchResultsPending: false,
        selectedArticles: [],
        loadedFeedQuery: meta.search.query,
        isSynced: meta.status === 'synced',
        isLoaded: true,
        isEditingFeed: true
      })
      .mergeIn(['advancedFilters'], {
        all: allFilters,
        selected: selectedFilters,
        pages: pages,
        pending: {}
      })
  },

  [SET_ACTIVE_FEED]: (state, {payload: feed}) => {
    return state.merge({
      'activeFeed': feed,
      'isEditingFeed': false
    })
  },

  [CHANGE_ACTIVE_FEED_NAME]: (state, {payload: feedName}) => {
    return state.setIn(['activeFeed', 'name'], feedName)
  },

  [TOGGLE_SAVE_FEED_POPUP]: (state, {payload}) => {
    const isVisible = !state.get('isSaveFeedPopupVisible')
    return state.set('isSaveFeedPopupVisible', isVisible)
  },

  [TOGGLE_REFINE_PANEL]: (state) => {
    const path = ['advancedFilters', 'isVisible']
    return state.setIn(path, !state.getIn(path))
  },

  [SELECT_REFINE_FILTER]: (state, {payload: {groupName, filterValue}}) => {
    const path = ['advancedFilters', 'selected', groupName]
    //two groups without multiple selection
    if (groupName === 'articleDate' || groupName === 'keyword') {
      state = state.deleteIn(path)
    }
    //tri-state switch
    const currentState = state.getIn([...path, filterValue])
    let newState
    if (currentState === undefined) {
      newState = 1
    }
    else if (currentState === 1) {
      newState = -1
    }
    return state.deleteIn(['advancedFilters', 'pending', groupName]).setIn([...path, filterValue], newState)
  },

  [CLEAR_REFINE_FILTERS]: (state, {payload: groupName}) => {
    return state.setIn(['advancedFilters', 'pending', groupName], true).deleteIn(['advancedFilters', 'selected', groupName])
  },

  [CLEAR_ALL_REFINE_FILTERS]: (state) => {
    return state.mergeIn(['advancedFilters'], {
      selected: {},
      pending: {}
    })
  },

  [LOAD_LESS_REFINE_FILTERS]: (state, {payload: groupName}) => {
    const path = ['advancedFilters', 'pages', groupName, 'count']
    const currentCount = state.getIn(path)
    return state.setIn(path, Math.max(currentCount - ADV_FILTERS_LIMIT, ADV_FILTERS_LIMIT))
  },

  [LOAD_MORE_REFINE_FILTERS]: (state, {payload: groupName}) => {
    const path = ['advancedFilters', 'pages', groupName]
    const currentCount = state.getIn([...path, 'count'])
    const totalCount = state.getIn([...path, 'totalCount'])
    return state.setIn([...path, 'count'], Math.min(currentCount + ADV_FILTERS_LIMIT, totalCount))
  },

  [EDIT_FEED]: (state) => {
    return state.set('isEditingFeed', true)
  },

  [SET_NEW_SEARCH]: (state) => {
    return state.merge(initialState.toJS())
  },

  [CHANGE_FEED_QUERY]: (state, {payload: value}) => {
    return state.merge({
      loadedFeedQuery: value,
      isEditingFeed: true
    })
  },

  [`${SAVE_FEED} pending`]: (state, {payload: {isPending}}) => {
    return state.set('isSavingFeed', isPending)
  },

  [`${SAVE_AS_FEED} pending`]: (state, {payload: {isPending}}) => {
    return state.set('isSavingFeed', isPending)
  },

  [SELECT_ARTICLE]: (state, {payload: article}) => {
    let selectedArticles = state.get('selectedArticles').toJS()
    const articleIndex = helpers.indexById(selectedArticles, article.id)
    if (articleIndex === -1) { //not selected yet
      selectedArticles = selectedArticles.concat(article)
    } else {
      selectedArticles.splice(articleIndex, 1)
    }
    return state.set('selectedArticles', fromJS(selectedArticles))
  },

  [SELECT_ALL_ARTICLES]: (state, {payload: select}) => {
    const selected = select ? state.get('searchResults').toJS() : fromJS([])
    return state.set('selectedArticles', selected)
  },

  [`${LOAD_MORE_COMMENTS} fulfilled`]: (state, {payload}) => {
    const {articleId, response} = payload
    const articles = state.get('searchResults')
    return state.set('searchResults', helpers.loadMoreComments(articles, articleId, response.data))
  },

  [`${COMMENT_ARTICLE} fulfilled`]: (state, {payload}) => {
    const {comment, articleId} = payload
    const articles = state.get('searchResults')
    return state.set('searchResults', helpers.addComment(articles, articleId, comment))
  },

  [`${UPDATE_COMMENT} fulfilled`]: (state, {payload}) => {
    const {comment, articleId} = payload
    const articles = state.get('searchResults')
    return state.set('searchResults', helpers.updateComment(articles, articleId, comment))
  },

  [`${DELETE_COMMENT} fulfilled`]: (state, {payload}) => {
    const {commentId, articleId} = payload
    const articles = state.get('searchResults')
    return state.set('searchResults', helpers.deleteComment(articles, articleId, commentId))
  },

  //only remove deleted articles the from selection
  [DELETE_ARTICLES]: deselectArticlesReducer,
  [`${DELETE_ARTICLES_FROM_FEED} fulfilled`]: deselectArticlesReducer

}, initialState)
