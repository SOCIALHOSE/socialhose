import {createAction, handleActions} from 'redux-actions'
import {fromJS} from 'immutable'
import * as categoriesApi from '../../../api/sidebarCategoriesApi'
import * as feedsApi from '../../../api/feedsApi'
import {addAlert} from '../common/alerts'
import {thunkAction} from '../../utils/common'
import * as helpers from '../../utils/helpers/sidebar'
import {getRestrictions} from '../common/auth'
import {actions as searchActions} from './search'
import {actions as searchFiltersActions} from './searchByFilters'

/*
 * Constants
 * */
export const TYPES = {
  FOLDER: 'folder',
  FEED: 'feed',
  CLIP_ARTICLE: 'clipArticle'
}

const NS = '[Sidebar]'
const GET_SIDEBAR_CATEGORIES = `${NS} Get categories`
const ADD_CLIPPINGS_FEED = `${NS} Add clippings feed`
const ADD_CATEGORY = `${NS} Add category`
const RENAME_FEED = `${NS} Rename feed`
const RENAME_CATEGORY = `${NS} Rename category`
const TOGGLE_EXPORT_FEED = `${NS} Toggle export feed`
const TOGGLE_EXPORT_CATEGORY = `${NS} Toggle export category`
const MOVE_FEED = `${NS} Move feed`
const MOVE_CATEGORY = `${NS} Move category`
const DELETE_FEED = `${NS} Delete feed`
const DELETE_CATEGORY = `${NS} Delete category`
const SET_CATEGORIES = `${NS} Set categories`

export const SET_FILTERED_CATEGORIES = 'SET_FILTERED_CATEGORIES'
export const CLEAR_FILTERED_CATEGORIES = 'CLEAR_FILTERED_CATEGORIES'

export const SHOW_DELETE_POPUP = 'SHOW_DELETE_POPUP'
export const HIDE_DELETE_POPUP = 'HIDE_DELETE_POPUP'
export const SHOW_RENAME_POPUP = 'SHOW_RENAME_POPUP'
export const HIDE_RENAME_POPUP = 'HIDE_RENAME_POPUP'
export const SHOW_ADD_CATEGORY_POPUP = 'SHOW_ADD_CATEGORY_POPUP'
export const HIDE_ADD_CATEGORY_POPUP = 'HIDE_ADD_CATEGORY_POPUP'

export const SHOW_ADD_CLIPPINGS_POPUP = 'SHOW_ADD_CLIPPINGS_POPUP'
export const HIDE_ADD_CLIPPINGS_POPUP = 'HIDE_ADD_CLIPPINGS_POPUP'

/*
 * Actions
 * */
export const getSidebarCategories = thunkAction(GET_SIDEBAR_CATEGORIES, ({token, fulfilled}) => {
  return categoriesApi
    .getCategories(token)
    .then((categories) => {
      fulfilled(categories)
    })
})

export const setFilteredCategories = createAction(SET_FILTERED_CATEGORIES, (filteredCategories) => filteredCategories)

export const _clearFilteredCategories = createAction(CLEAR_FILTERED_CATEGORIES)

export const clearFilteredCategories = () => {
  return (dispatch, state) => {
    document.getElementById('sidebar-search').value = ''

    dispatch(_clearFilteredCategories())
  }
}

export const setCategories = createAction(SET_CATEGORIES, (changedCategories) => changedCategories)

export const showDeletePopup = createAction(SHOW_DELETE_POPUP, (itemId, itemType, itemName, parentId) => {
  return {itemId, itemType, itemName, parentId}
})
export const hideDeletePopup = createAction(HIDE_DELETE_POPUP)

export const showRenamePopup = createAction(SHOW_RENAME_POPUP, (itemId, itemType, itemName, parentId) => {
  return {itemId, itemType, itemName, parentId}
})
export const hideRenamePopup = createAction(HIDE_RENAME_POPUP)

export const showAddCategoryPopup = createAction(SHOW_ADD_CATEGORY_POPUP, (parentId) => ({parentId}))
export const hideAddCategoryPopup = createAction(HIDE_ADD_CATEGORY_POPUP)

export const showAddClippingsFeedPopup = createAction(SHOW_ADD_CLIPPINGS_POPUP, (parentId) => ({parentId}))
export const hideAddClippingsFeedPopup = createAction(HIDE_ADD_CLIPPINGS_POPUP)

export const addCategory = thunkAction(ADD_CATEGORY, (name, parentId, {token, dispatch, getState, fulfilled}) => {
  return categoriesApi
    .addCategory(token, {name: name, parent: parentId})
    .then((newCategory) => {
      fulfilled(newCategory)
      const newCategories = helpers.addCategory(getState(), parentId, newCategory)
      dispatch(setCategories(newCategories))
      dispatch(clearFilteredCategories())
    })
})

export const moveCategory = thunkAction(MOVE_CATEGORY, (category, newCategoryId, {token, dispatch, fulfilled}) => {
  const draggedCategoryId = category.id
  const notCategoryItself = newCategoryId !== draggedCategoryId
  const notCategoryInChild = !helpers.checkIfDraggedCategoryDragToItsChild(category.childes, newCategoryId)
  // check if category trying to move to it's child or itself
  if (notCategoryItself && notCategoryInChild) {
    return categoriesApi
      .moveCategory(token, undefined, draggedCategoryId, newCategoryId)
      .then((response) => {
        fulfilled()
        const newCategories = fromJS(response.data)
        dispatch(setCategories(newCategories))
      })
  }
})

export const renameCategory = thunkAction(RENAME_CATEGORY, (categoryId, categoryName, parentId, {token, dispatch, getState, fulfilled}) => {
  return categoriesApi
    .renameCategory(token, {name: categoryName, parent: parentId}, categoryId)
    .then(() => {
      fulfilled()
      const newCategories = helpers.renameCategory(getState(), categoryId, categoryName)
      dispatch(setCategories(newCategories))
      dispatch(clearFilteredCategories())
    })
})

export const deleteCategory = thunkAction(DELETE_CATEGORY, (categoryId, {token, dispatch, getState, fulfilled}) => {
  return categoriesApi
    .deleteCategory(token, undefined, categoryId)
    .then(() => {
      fulfilled()
      const newCategories = helpers.deleteCategory(getState(), categoryId)
      dispatch(setCategories(newCategories))
      dispatch(clearFilteredCategories())
    })
})

export const addClippingsFeed = thunkAction(ADD_CLIPPINGS_FEED, (feedName, categoryId, {token, dispatch, fulfilled, getState}) => {
  const payload = {
    feed: {
      name: feedName,
      category: categoryId,
      subType: 'clip_feed'
    }
  }
  return feedsApi
    .createFeed(token, payload)
    .then((newFeed) => {
      fulfilled(newFeed)
      const newCategories = helpers.addFeed(getState(), categoryId, newFeed)
      dispatch(setCategories(newCategories))
      dispatch(getRestrictions())
      dispatch(addAlert({
        type: 'notice',
        transKey: 'saveFeed',
        id: 'saveFeed'
      }))
    })
})

export const moveFeed = thunkAction(MOVE_FEED, (feedId, categoryId, {token, dispatch, fulfilled}) => {
  return feedsApi
    .moveFeed(token, undefined, feedId, categoryId)
    .then((response) => {
      fulfilled()
      const newCategories = fromJS(response.data)
      dispatch(setCategories(newCategories))
    })
})

export const toggleExportFeed = thunkAction(TOGGLE_EXPORT_FEED, (feedId, isExported, {token, dispatch, fulfilled}) => {
  return feedsApi
    .toggleExportFeed(token, {export: isExported}, feedId)
    .then(() => {
      fulfilled()
      dispatch(getSidebarCategories())
      dispatch(getRestrictions())
    })
})

const toggleExportCategory = thunkAction(TOGGLE_EXPORT_CATEGORY, (categoryId, isExported, {token, dispatch, fulfilled}) => {
  return feedsApi
    .toggleExportCategory(token, {export: isExported}, categoryId)
    .then(() => {
      fulfilled()
      dispatch(getSidebarCategories())
    })
})

export const renameFeed = thunkAction(RENAME_FEED, (feedId, newName, parentId, {token, dispatch, fulfilled, getState}) => {
  return feedsApi
    .renameFeed(token, {name: newName}, feedId)
    .then(() => {
      fulfilled()
      const newCategories = helpers.renameFeed(getState(), feedId, newName, parentId)
      dispatch(setCategories(newCategories))
    })
})

export const deleteFeed = thunkAction(DELETE_FEED, (feedId, categoryId, {token, dispatch, getState, fulfilled}) => {
  const currentFeedId = getState().getIn(['appState', 'search', 'activeFeed', 'id'])
  const isCurrent = currentFeedId && (parseInt(currentFeedId) === parseInt(feedId))
  return feedsApi
    .deleteFeed(token, undefined, feedId)
    .then(() => {
      fulfilled()
      if (isCurrent) {
        dispatch(searchActions.setNewSearch())
        dispatch(searchFiltersActions.renewSearchBy())
      }
      const newCategories = helpers.deleteFeed(getState(), categoryId, feedId)
      dispatch(setCategories(newCategories))
      dispatch(getRestrictions())
      dispatch(clearFilteredCategories())
    })
})

export const actions = {
  getSidebarCategories,
  setFilteredCategories,
  clearFilteredCategories,
  setCategories,
  addCategory,
  addClippingsFeed,
  moveCategory,
  moveFeed,
  deleteFeed,
  deleteCategory,
  renameFeed,
  renameCategory,
  showDeletePopup,
  hideDeletePopup,
  showRenamePopup,
  hideRenamePopup,
  showAddCategoryPopup,
  hideAddCategoryPopup,
  showAddClippingsFeedPopup,
  hideAddClippingsFeedPopup,
  toggleExportFeed,
  toggleExportCategory
}

/*
 * State
 * */
export const initialState = fromJS({
  areCategoriesLoaded: false,
  categories: [],
  filteredCategories: [],
  areFeedsFiltered: false,
  popupVisible: {
    'delete': false,
    rename: false,
    addCategory: false,
    addClippingsFeed: false
  },
  popupItems: {
    'delete': {}, //feed or category
    rename: {}, //feed or category
    addCategory: {}, //{parentId}
    addClippingsFeed: {} //{parentId}
  }
})

/*
 * Reducers
 * */
const hidePopup = (type) => (state) => {
  return state
    .setIn(['popupVisible', type], false)
    .setIn(['popupItems', type], {})
}

const showPopup = (type) => (state, {payload}) => {
  return state
    .setIn(['popupVisible', type], true)
    .setIn(['popupItems', type], payload)
}

export default handleActions({

  [`${GET_SIDEBAR_CATEGORIES} fulfilled`]: (state, { payload }) => {
    const response = payload.data

    return state.merge({
      'categories': response,
      'areCategoriesLoaded': true
    })
  },

  [SET_FILTERED_CATEGORIES]: (state, { payload: filteredCategories }) => {
    return state.merge({
      'filteredCategories': filteredCategories,
      'areFeedsFiltered': true
    })
  },

  [CLEAR_FILTERED_CATEGORIES]: (state, { payload }) => {
    return state.merge({
      'filteredCategories': [],
      'areFeedsFiltered': false
    })
  },

  [SET_CATEGORIES]: (state, { payload: changedCategories }) => {
    return state.set('categories', changedCategories)
  },

  [SHOW_DELETE_POPUP]: showPopup('delete'),

  [HIDE_DELETE_POPUP]: hidePopup('delete'),

  [SHOW_RENAME_POPUP]: showPopup('rename'),

  [HIDE_RENAME_POPUP]: hidePopup('rename'),

  [SHOW_ADD_CATEGORY_POPUP]: showPopup('addCategory'),

  [HIDE_ADD_CATEGORY_POPUP]: hidePopup('addCategory'),

  [SHOW_ADD_CLIPPINGS_POPUP]: showPopup('addClippingsFeed'),

  [HIDE_ADD_CLIPPINGS_POPUP]: hidePopup('addClippingsFeed')

}, initialState)
