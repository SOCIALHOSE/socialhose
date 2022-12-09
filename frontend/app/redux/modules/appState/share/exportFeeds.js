import {handleActions, createAction} from 'redux-actions'
import {fromJS} from 'immutable'
import {thunkAction} from '../../../utils/common'
import * as api from '../../../../api/feedsApi'
import {getSidebarCategories} from '../sidebar'
import { getRestrictions } from '../../common/auth'

/** CONSTANTS **/
const NS = '[Export]'
const LOAD_EXPORTED_FEEDS = `${NS} Load exported feeds`
const SHOW_EXPORT_POPUP = `${NS} Show export popup`
const HIDE_EXPORT_POPUP = `${NS} Hide export popup`
const UNEXPORT_FEED = `${NS} Unexport feed`

/** ACTIONS **/

const loadExportedFeeds = thunkAction(LOAD_EXPORTED_FEEDS, ({token, fulfilled}) => {
  return api
    .loadExportedFeeds(token)
    .then((data) => {
      fulfilled(data)
    })
}, true)

const showExportPopup = createAction(SHOW_EXPORT_POPUP, (feed, exportFormat) => ({feed, exportFormat}))
const hideExportPopup = createAction(HIDE_EXPORT_POPUP)

const unexportFeed = thunkAction(UNEXPORT_FEED, (feedId, {token, fulfilled, dispatch}) => {
  return api
    .toggleExportFeed(token, {export: false}, feedId)
    .then(() => {
      fulfilled()
      dispatch(getSidebarCategories())
      dispatch(getRestrictions())
      dispatch(loadExportedFeeds())
    })
})

export const actions = {
  loadExportedFeeds,
  showExportPopup,
  hideExportPopup,
  unexportFeed
}

//**** STATE ****//
export const initialState = fromJS({
  isLoading: false,
  tableData: [],
  popupVisible: false,
  selectedFeed: null,
  exportFormat: ''
})

export default handleActions({

  [`${LOAD_EXPORTED_FEEDS} pending`]: (state, {payload: {isPending}}) => {
    return state.set('isLoading', isPending)
  },

  [`${LOAD_EXPORTED_FEEDS} fulfilled`]: (state, {payload: data}) => {
    return state.set('tableData', data)
  },

  [SHOW_EXPORT_POPUP]: (state, {payload: {feed, exportFormat}}) => {
    return state.merge({
      selectedFeed: feed,
      popupVisible: true,
      exportFormat
    })
  },

  [HIDE_EXPORT_POPUP]: (state) => {
    return state.set('popupVisible', false)
  }

}, initialState)
