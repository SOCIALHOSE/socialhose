import { createAction, handleActions } from 'redux-actions'
import { fromJS } from 'immutable'

import { searchSources, getSourceLists } from '../../../api/searchApi'
import {thunkAction} from '../../utils/common'
import { storeObj } from '../../../main'
/*
 * Constants
 * */
export const TOGGLE_MEDIA_TYPE = 'TOGGLE_MEDIA_TYPE'
export const TOGGLE_ALL_MEDIA_TYPES = 'TOGGLE_ALL_MEDIA_TYPES'

export const SET_SEARCH_INTERVAL = 'SET_SEARCH_INTERVAL'
export const SET_SEARCH_LAST_DATE = 'SET_SEARCH_LAST_DATE'
export const SET_SEARCH_DATE = 'SET_SEARCH_DATE'
export const SET_START_DATE = 'SET_START_DATE'
export const SET_END_DATE = 'SET_END_DATE'

export const TOGGLE_SEARCH_BY = 'TOGGLE_SEARCH_BY'
export const CHOOSE_SEARCH_BY_TAB = 'CHOOSE_SEARCH_BY_TAB'

export const SET_HEADLINE_INCLUDED = 'SET_HEADLINE_INCLUDED'
export const SET_HEADLINE_EXCLUDED = 'SET_HEADLINE_EXCLUDED'

export const TOGGLE_LANG = 'TOGGLE_LANG'
export const TOGGLE_ALL_LANGS = 'TOGGLE_ALL_LANGS'

export const CHANGE_LOCATIONS_TYPE = 'CHANGE_LOCATIONS_TYPE'

export const MOVE_LOCATION = 'MOVE_LOCATION'

export const CLEAR_LOCATIONS = 'CLEAR_LOCATIONS'

export const GET_SEARCH_BY_SOURCES = 'GET_SEARCH_BY_SOURCES'
export const SET_SEARCH_BY_SOURCES_QUERY = 'SET_SEARCH_BY_SOURCES_QUERY'

export const ADD_SELECTED_SEARCH_BY_SOURCE = 'ADD_SELECTED_SEARCH_BY_SOURCE'
export const REMOVE_SELECTED_SEARCH_BY_SOURCE = 'REMOVE_SELECTED_SEARCH_BY_SOURCE'

export const INCLUDE_EXCLUDE_SEARCH_BY_SOURCES = 'INCLUDE_EXCLUDE_SEARCH_BY_SOURCES'
export const CLEAR_SEARCH_BY_SOURCES = 'CLEAR_SEARCH_BY_SOURCES'

export const GET_SEARCH_BY_SOURCE_LISTS = 'GET_SEARCH_BY_SOURCE_LISTS'

export const MOVE_SOURCE_LIST = 'MOVE_SOURCE_LIST'

export const TOGGLE_INCLUDE_DUPLICATES = 'TOGGLE_INCLUDE_DUPLICATES'
export const TOGGLE_HAS_IMAGES = 'TOGGLE_HAS_IMAGES'

export const RENEW_SEARCH_BY = 'RENEW_SEARCH_BY'
export const SET_COMMON_FILTERS = 'SET_COMMON_FILTERS'

/*
 * Actions
 * */
export const toggleMediaType = createAction(TOGGLE_MEDIA_TYPE, (chosenType, isChosen) => {
  return {chosenType, isChosen}
})
export const toggleAllMediaTypes = createAction(TOGGLE_ALL_MEDIA_TYPES, isChosen => isChosen)

export const setSearchInterval = createAction(SET_SEARCH_INTERVAL, (newInterval) => newInterval)
export const setSearchLastDate = createAction(SET_SEARCH_LAST_DATE, (newLastDate) => newLastDate)
export const setSearchDate = createAction(SET_SEARCH_DATE, (newDate) => newDate)
export const setStartDate = createAction(SET_START_DATE, (newDate) => newDate)
export const setEndDate = createAction(SET_END_DATE, (newDate) => newDate)

export const toggleSearchBy = createAction(TOGGLE_SEARCH_BY)
export const chooseSearchByTab = createAction(CHOOSE_SEARCH_BY_TAB, (tabName) => tabName)

export const setHeadlineIncluded = createAction(SET_HEADLINE_INCLUDED, (headline) => headline)
export const setHeadlineExcluded = createAction(SET_HEADLINE_EXCLUDED, (headline) => headline)

export const toggleLang = createAction(TOGGLE_LANG, (chosenLang, isChosen) => {
  return {chosenLang, isChosen}
})
export const toggleAllLangs = createAction(TOGGLE_ALL_LANGS, isChosen => isChosen)

export const changeLocationsType = createAction(CHANGE_LOCATIONS_TYPE, (newLocationsType) => newLocationsType)

export const moveLocation = createAction(MOVE_LOCATION, (from, to, locType, loc) => {
  return {from, to, locType, loc}
})

export const clearLocations = createAction(CLEAR_LOCATIONS)

export const getSearchBySources = thunkAction(GET_SEARCH_BY_SOURCES, (data, {token, fulfilled}) => {
  return searchSources(token, data)
    .then((data) => {
      fulfilled(data)
    })
})
export const setSearchBySourcesQuery = createAction(SET_SEARCH_BY_SOURCES_QUERY, (query) => query)

export const addSelectedSearchBySource = createAction(ADD_SELECTED_SEARCH_BY_SOURCE, (source) => source)

export const removeSelectedSearchBySource = createAction(REMOVE_SELECTED_SEARCH_BY_SOURCE, (sourceId) => sourceId)
export const clearSearchBySources = createAction(CLEAR_SEARCH_BY_SOURCES)

export const includeExcludeSearchBySources = createAction(INCLUDE_EXCLUDE_SEARCH_BY_SOURCES, (sourcesType) => sourcesType)

export const getSearchBySourceLists = thunkAction(GET_SEARCH_BY_SOURCE_LISTS, (data, {token, fulfilled}) => {
  return getSourceLists(token, data)
    .then((data) => {
      fulfilled(data)
    })
})

export const moveSourceList = createAction(MOVE_SOURCE_LIST, (from, to, list) => {
  return {from, to, list}
})

export const toggleIncludeDuplicates = createAction(TOGGLE_INCLUDE_DUPLICATES)
export const toggleHasImages = createAction(TOGGLE_HAS_IMAGES)

export const renewSearchBy = createAction(RENEW_SEARCH_BY)

export const setCommonFilters = createAction(SET_COMMON_FILTERS, (filters, sourceLists, sources) => {
  return {filters, sourceLists, sources}
})

export const actions = {
  toggleMediaType,
  toggleAllMediaTypes,
  setSearchInterval,
  setSearchLastDate,
  setSearchDate,
  setStartDate,
  setEndDate,
  toggleSearchBy,
  chooseSearchByTab,
  setHeadlineIncluded,
  setHeadlineExcluded,
  toggleLang,
  toggleAllLangs,
  changeLocationsType,
  moveLocation,
  clearLocations,
  getSearchBySources,
  setSearchBySourcesQuery,
  addSelectedSearchBySource,
  removeSelectedSearchBySource,
  clearSearchBySources,
  includeExcludeSearchBySources,
  getSearchBySourceLists,
  moveSourceList,
  toggleIncludeDuplicates,
  toggleHasImages,
  renewSearchBy,
  setCommonFilters
}

/*
 * State
 * */
const locations = [{code: 'AD', type: 'country'}, {code: 'AE', type: 'country'}, {code: 'AF', type: 'country'}, {code: 'AG', type: 'country'}, {code: 'AI', type: 'country'}, {code: 'AL', type: 'country'}, {code: 'AM', type: 'country'}, {code: 'AO', type: 'country'}, {code: 'AQ', type: 'country'}, {code: 'AR', type: 'country'}, {code: 'AS', type: 'country'},
  {code: 'AT', type: 'country'}, {code: 'AU', type: 'country'}, {code: 'AW', type: 'country'}, {code: 'AX', type: 'country'}, {code: 'AZ', type: 'country'}, {code: 'BA', type: 'country'}, {code: 'BB', type: 'country'}, {code: 'BD', type: 'country'}, {code: 'BE', type: 'country'}, {code: 'BF', type: 'country'}, {code: 'BG', type: 'country'}, {code: 'BH', type: 'country'}, {code: 'BI', type: 'country'}, {code: 'BJ', type: 'country'},
  {code: 'BL', type: 'country'}, {code: 'BM', type: 'country'}, {code: 'BN', type: 'country'}, {code: 'BO', type: 'country'}, {code: 'BQ', type: 'country'}, {code: 'BR', type: 'country'}, {code: 'BS', type: 'country'}, {code: 'BT', type: 'country'}, {code: 'BV', type: 'country'}, {code: 'BW', type: 'country'}, {code: 'BY', type: 'country'}, {code: 'BZ', type: 'country'}, {code: 'CA', type: 'country'}, {code: 'CC', type: 'country'},
  {code: 'CD', type: 'country'}, {code: 'CF', type: 'country'}, {code: 'CG', type: 'country'}, {code: 'CH', type: 'country'}, {code: 'CI', type: 'country'}, {code: 'CK', type: 'country'}, {code: 'CL', type: 'country'}, {code: 'CM', type: 'country'}, {code: 'CN', type: 'country'}, {code: 'CO', type: 'country'}, {code: 'CR', type: 'country'}, {code: 'CU', type: 'country'}, {code: 'CV', type: 'country'}, {code: 'CW', type: 'country'},
  {code: 'CX', type: 'country'}, {code: 'CY', type: 'country'}, {code: 'CZ', type: 'country'}, {code: 'DE', type: 'country'}, {code: 'DJ', type: 'country'}, {code: 'DK', type: 'country'}, {code: 'DM', type: 'country'}, {code: 'DO', type: 'country'}, {code: 'DZ', type: 'country'}, {code: 'EC', type: 'country'}, {code: 'EE', type: 'country'}, {code: 'EG', type: 'country'}, {code: 'EH', type: 'country'}, {code: 'ER', type: 'country'},
  {code: 'ES', type: 'country'}, {code: 'ET', type: 'country'}, {code: 'FI', type: 'country'}, {code: 'FJ', type: 'country'}, {code: 'FK', type: 'country'}, {code: 'FM', type: 'country'}, {code: 'FO', type: 'country'}, {code: 'FR', type: 'country'}, {code: 'GA', type: 'country'}, {code: 'GB', type: 'country'}, {code: 'GD', type: 'country'}, {code: 'GE', type: 'country'}, {code: 'GF', type: 'country'}, {code: 'GG', type: 'country'},
  {code: 'GH', type: 'country'}, {code: 'GI', type: 'country'}, {code: 'GL', type: 'country'}, {code: 'GM', type: 'country'}, {code: 'GN', type: 'country'}, {code: 'GP', type: 'country'}, {code: 'GQ', type: 'country'}, {code: 'GR', type: 'country'}, {code: 'GS', type: 'country'}, {code: 'GT', type: 'country'}, {code: 'GU', type: 'country'}, {code: 'GW', type: 'country'}, {code: 'GY', type: 'country'}, {code: 'HK', type: 'country'},
  {code: 'HM', type: 'country'}, {code: 'HN', type: 'country'}, {code: 'HR', type: 'country'}, {code: 'HT', type: 'country'}, {code: 'HU', type: 'country'}, {code: 'ID', type: 'country'}, {code: 'IE', type: 'country'}, {code: 'IL', type: 'country'}, {code: 'IM', type: 'country'}, {code: 'IN', type: 'country'}, {code: 'IO', type: 'country'}, {code: 'IQ', type: 'country'}, {code: 'IR', type: 'country'}, {code: 'IS', type: 'country'},
  {code: 'IT', type: 'country'}, {code: 'JE', type: 'country'}, {code: 'JM', type: 'country'}, {code: 'JO', type: 'country'}, {code: 'JP', type: 'country'}, {code: 'KE', type: 'country'}, {code: 'KG', type: 'country'}, {code: 'KH', type: 'country'}, {code: 'KI', type: 'country'}, {code: 'KM', type: 'country'}, {code: 'KN', type: 'country'}, {code: 'KP', type: 'country'}, {code: 'KR', type: 'country'}, {code: 'KW', type: 'country'},
  {code: 'KY', type: 'country'}, {code: 'KZ', type: 'country'}, {code: 'LA', type: 'country'}, {code: 'LB', type: 'country'}, {code: 'LC', type: 'country'}, {code: 'LI', type: 'country'}, {code: 'LK', type: 'country'}, {code: 'LR', type: 'country'}, {code: 'LS', type: 'country'}, {code: 'LT', type: 'country'}, {code: 'LU', type: 'country'}, {code: 'LV', type: 'country'}, {code: 'LY', type: 'country'}, {code: 'MA', type: 'country'},
  {code: 'MC', type: 'country'}, {code: 'MD', type: 'country'}, {code: 'ME', type: 'country'}, {code: 'MF', type: 'country'}, {code: 'MG', type: 'country'}, {code: 'MH', type: 'country'}, {code: 'MK', type: 'country'}, {code: 'ML', type: 'country'}, {code: 'MM', type: 'country'}, {code: 'MN', type: 'country'}, {code: 'MO', type: 'country'}, {code: 'MP', type: 'country'}, {code: 'MQ', type: 'country'}, {code: 'MR', type: 'country'},
  {code: 'MS', type: 'country'}, {code: 'MT', type: 'country'}, {code: 'MU', type: 'country'}, {code: 'MV', type: 'country'}, {code: 'MW', type: 'country'}, {code: 'MX', type: 'country'}, {code: 'MY', type: 'country'}, {code: 'MZ', type: 'country'}, {code: 'NA', type: 'country'}, {code: 'NC', type: 'country'}, {code: 'NE', type: 'country'}, {code: 'NF', type: 'country'}, {code: 'NG', type: 'country'}, {code: 'NI', type: 'country'},
  {code: 'NL', type: 'country'}, {code: 'NO', type: 'country'}, {code: 'NP', type: 'country'}, {code: 'NR', type: 'country'}, {code: 'NU', type: 'country'}, {code: 'NZ', type: 'country'}, {code: 'OM', type: 'country'}, {code: 'PA', type: 'country'}, {code: 'PE', type: 'country'}, {code: 'PF', type: 'country'}, {code: 'PG', type: 'country'}, {code: 'PH', type: 'country'}, {code: 'PK', type: 'country'}, {code: 'PL', type: 'country'},
  {code: 'PM', type: 'country'}, {code: 'PN', type: 'country'}, {code: 'PR', type: 'country'}, {code: 'PS', type: 'country'}, {code: 'PT', type: 'country'}, {code: 'PW', type: 'country'}, {code: 'PY', type: 'country'}, {code: 'QA', type: 'country'}, {code: 'RE', type: 'country'}, {code: 'RO', type: 'country'}, {code: 'RS', type: 'country'}, {code: 'RU', type: 'country'}, {code: 'RW', type: 'country'}, {code: 'SA', type: 'country'},
  {code: 'SB', type: 'country'}, {code: 'SC', type: 'country'}, {code: 'SD', type: 'country'}, {code: 'SE', type: 'country'}, {code: 'SG', type: 'country'}, {code: 'SH', type: 'country'}, {code: 'SI', type: 'country'}, {code: 'SJ', type: 'country'}, {code: 'SK', type: 'country'}, {code: 'SL', type: 'country'}, {code: 'SM', type: 'country'}, {code: 'SN', type: 'country'}, {code: 'SO', type: 'country'}, {code: 'SR', type: 'country'},
  {code: 'SS', type: 'country'}, {code: 'ST', type: 'country'}, {code: 'SV', type: 'country'}, {code: 'SX', type: 'country'}, {code: 'SY', type: 'country'}, {code: 'SZ', type: 'country'}, {code: 'TC', type: 'country'}, {code: 'TD', type: 'country'}, {code: 'TF', type: 'country'}, {code: 'TG', type: 'country'}, {code: 'TH', type: 'country'}, {code: 'TJ', type: 'country'}, {code: 'TK', type: 'country'}, {code: 'TL', type: 'country'},
  {code: 'TM', type: 'country'}, {code: 'TN', type: 'country'}, {code: 'TO', type: 'country'}, {code: 'TR', type: 'country'}, {code: 'TT', type: 'country'}, {code: 'TV', type: 'country'}, {code: 'TW', type: 'country'}, {code: 'TZ', type: 'country'}, {code: 'UA', type: 'country'}, {code: 'UG', type: 'country'}, {code: 'UM', type: 'country'}, {code: 'US', type: 'country'}, {code: 'UY', type: 'country'}, {code: 'UZ', type: 'country'},
  {code: 'VA', type: 'country'}, {code: 'VC', type: 'country'}, {code: 'VE', type: 'country'}, {code: 'VG', type: 'country'}, {code: 'VI', type: 'country'}, {code: 'VN', type: 'country'}, {code: 'VU', type: 'country'}, {code: 'WF', type: 'country'}, {code: 'WS', type: 'country'}, {code: 'YE', type: 'country'}, {code: 'YT', type: 'country'}, {code: 'ZA', type: 'country'}, {code: 'ZM', type: 'country'}, {code: 'ZW', type: 'country'}, {code: 'AL', type: 'state'}, {code: 'AK', type: 'state'}, {code: 'AZ', type: 'state'}, {code: 'AR', type: 'state'}, {code: 'CA', type: 'state'}, {code: 'CO', type: 'state'}, {code: 'CT', type: 'state'}, {code: 'DE', type: 'state'}, {code: 'DC', type: 'state'}, {code: 'FL', type: 'state'}, {code: 'GA', type: 'state'}, {code: 'HI', type: 'state'},
  {code: 'ID', type: 'state'}, {code: 'IL', type: 'state'}, {code: 'IN', type: 'state'}, {code: 'IA', type: 'state'}, {code: 'KS', type: 'state'}, {code: 'KY', type: 'state'}, {code: 'LA', type: 'state'}, {code: 'ME', type: 'state'}, {code: 'MD', type: 'state'}, {code: 'MA', type: 'state'}, {code: 'MI', type: 'state'}, {code: 'MN', type: 'state'}, {code: 'MS', type: 'state'}, {code: 'MO', type: 'state'},
  {code: 'MT', type: 'state'}, {code: 'NE', type: 'state'}, {code: 'NV', type: 'state'}, {code: 'NH', type: 'state'}, {code: 'NJ', type: 'state'}, {code: 'NM', type: 'state'}, {code: 'NY', type: 'state'}, {code: 'NC', type: 'state'}, {code: 'ND', type: 'state'}, {code: 'OH', type: 'state'}, {code: 'OK', type: 'state'}, {code: 'OR', type: 'state'}, {code: 'PA', type: 'state'}, {code: 'RI', type: 'state'},
  {code: 'SC', type: 'state'}, {code: 'SD', type: 'state'}, {code: 'TN', type: 'state'}, {code: 'TX', type: 'state'}, {code: 'UT', type: 'state'}, {code: 'VT', type: 'state'}, {code: 'VA', type: 'state'}, {code: 'WA', type: 'state'}, {code: 'WV', type: 'state'}, {code: 'WI', type: 'state'}, {code: 'WY', type: 'state'}]

export const allMediaTypes = [ // last 3 are domain params
  'news',
  'blogs',
  'reddit',
  'twitter',
  'instagram'
];

export const initialState = fromJS({
  // mediaTypes: ['news', 'blogs', 'socials', 'videos', 'forums', 'photo'],
  mediaTypes: allMediaTypes,
  // chosenMediaTypes: ['news', 'blogs', 'socials', 'videos', 'forums', 'photo'],
  chosenMediaTypes: [], // set only the allowed media types from restrictions initially
  searchLastDates: ['1d', '7d', '15d', '30d'], // 15d as 2W, to match with subscription
  searchIntervals: ['all', 'last', 'between'],
  chosenSearchDate: 'all',
  chosenSearchInterval: 'all',
  chosenSearchLastDate: '1d',
  chosenStartDate: '',
  chosenEndDate: '',
  isSearchByVisible: false,
  searchByTabs: [
    'emphasis', 'languages', 'locations', 'sources', 'sourceLists', 'extras'
  ],
  chosenSearchByTab: 'emphasis',
  searchLanguages: ['af', 'sq', 'ar', 'bn', 'bs', 'bg', 'ca', 'zh', 'hr', 'cs',
    'da', 'nl', 'en', 'et', 'tl', 'fi', 'fr', 'de', 'el', 'he', 'hi', 'hu', 'is',
    'id', 'it', 'ja', 'ko', 'lv', 'lt', 'mk', 'ms', 'no', 'fa', 'pl', 'pt', 'ro',
    'ru', 'sr', 'sk', 'sl', 'es', 'sv', 'ta', 'th', 'tr', 'uk', 'ur', 'vi'],
  chosenLanguages: [],
  locations,
  chosenLocationsType: 'country',
  locationsToInclude: [],
  locationsToExclude: [],
  headlineIncluded: '',
  headlineExcluded: '',
  searchBySourcesQuery: '',
  searchBySources: [],
  selectedSearchBySources: [],
  searchBySourcesType: 'include',
  searchBySourceLists: [],
  searchBySourceListsAvailable: [],
  searchBySourceListsToInclude: [],
  searchBySourceListsToExclude: [],
  hasImages: false
})

/*
 * Reducers
 * */
export default handleActions({

  [TOGGLE_MEDIA_TYPE]: (state, {payload}) => {
    const chosenTypes = state.get('chosenMediaTypes')

    const newChosenTypes = payload.isChosen
      ? chosenTypes.concat(payload.chosenType)
      : chosenTypes.filter((type) => {
        return payload.chosenType !== type
      })

    return state.set('chosenMediaTypes', newChosenTypes)
  },

  [TOGGLE_ALL_MEDIA_TYPES]: (state, {payload: isChosen}) => {
    const chosenTypes = isChosen ? state.get('mediaTypes').toJS() : []
    return state.set('chosenMediaTypes', chosenTypes)
  },

  [SET_SEARCH_INTERVAL]: (state, {payload: newInterval}) => {
    return state.set('chosenSearchInterval', newInterval)
  },

  [SET_SEARCH_LAST_DATE]: (state, {payload: newLastDate}) => {
    return state.set('chosenSearchLastDate', newLastDate)
  },

  [SET_SEARCH_DATE]: (state, {payload: newDate}) => {
    return state.set('chosenSearchDate', newDate)
  },

  [SET_START_DATE]: (state, {payload: newDate}) => {
    return state.set('chosenStartDate', newDate)
  },

  [SET_END_DATE]: (state, {payload: newDate}) => {
    return state.set('chosenEndDate', newDate)
  },

  [TOGGLE_SEARCH_BY]: (state, {payload}) => {
    const isSearchByVisible = !state.get('isSearchByVisible')
    return state.set('isSearchByVisible', isSearchByVisible)
  },

  [CHOOSE_SEARCH_BY_TAB]: (state, {payload: tabName}) => {
    return state.set('chosenSearchByTab', tabName)
  },

  [SET_HEADLINE_INCLUDED]: (state, {payload: headline}) => {
    return state.set('headlineIncluded', headline)
  },

  [SET_HEADLINE_EXCLUDED]: (state, {payload: headline}) => {
    return state.set('headlineExcluded', headline)
  },

  [TOGGLE_LANG]: (state, {payload}) => {
    const chosenLangs = state.get('chosenLanguages')

    const newChosenLangs = payload.isChosen
      ? chosenLangs.concat(payload.chosenLang)
      : chosenLangs.filter((lang) => {
        return payload.chosenLang !== lang
      })

    return state.set('chosenLanguages', newChosenLangs)
  },

  [TOGGLE_ALL_LANGS]: (state, {payload: isChosen}) => {
    const chosenLangs = isChosen ? state.get('searchLanguages').toJS() : []
    return state.set('chosenLanguages', chosenLangs)
  },

  [CHANGE_LOCATIONS_TYPE]: (state, {payload: newLocationsType}) => {
    return state.set('chosenLocationsType', newLocationsType)
  },

  [MOVE_LOCATION]: (state, { payload }) => {
    const listFrom = state.get(payload.from)
    const filteredList = listFrom.filter((loc) => {
      return loc.get('code') !== payload.loc.code
    })
    const listTo = state.get(payload.to).push(fromJS(payload.loc))

    return state.merge({
      [payload.from]: filteredList,
      [payload.to]: listTo
    })
  },

  [CLEAR_LOCATIONS]: (state) => {
    const locations = initialState.get('locations')
    const chosenLocationsType = initialState.get('chosenLocationsType')
    const locationsToInclude = initialState.get('locationsToInclude')
    const locationsToExclude = initialState.get('locationsToExclude')

    return state.merge({
      locations,
      chosenLocationsType,
      locationsToInclude,
      locationsToExclude
    })
  },

  [`${GET_SEARCH_BY_SOURCES} fulfilled`]: (state, { payload }) => {
    const response = payload.sources.data
    return state.set('searchBySources', response)
  },

  [SET_SEARCH_BY_SOURCES_QUERY]: (state, {payload: query}) => {
    return state.set('searchBySourcesQuery', query)
  },

  [ADD_SELECTED_SEARCH_BY_SOURCE]: (state, {payload: source}) => {
    const selectedSources = state.get('selectedSearchBySources')
    const isNew = !selectedSources.find(chosenSource => chosenSource.get('id') === source.id)
    const newSources = isNew ? selectedSources.push(fromJS(source)) : selectedSources

    return state.set('selectedSearchBySources', newSources)
  },

  [REMOVE_SELECTED_SEARCH_BY_SOURCE]: (state, {payload: sourceId}) => {
    const newSources = state.get('selectedSearchBySources').filter((sourceItem) => {
      return sourceItem.get('id') !== sourceId
    })
    return state.set('selectedSearchBySources', newSources)
  },

  [CLEAR_SEARCH_BY_SOURCES]: (state) => {
    return state.merge({
      'selectedSearchBySources': fromJS([])
    })
  },

  [INCLUDE_EXCLUDE_SEARCH_BY_SOURCES]: (state, {payload: sourceType}) => {
    return state.set('searchBySourcesType', sourceType)
  },

  [`${GET_SEARCH_BY_SOURCE_LISTS} fulfilled`]: (state, { payload }) => {
    const includedListsIds = state.get('searchBySourceListsToInclude').map(list => list.get('id'))
    const excludedListsIds = state.get('searchBySourceListsToExclude').map(list => list.get('id'))
    const usedListsIds = includedListsIds.concat(excludedListsIds)

    const allLists = fromJS(payload.data)
    const lists = allLists.filter(list => !usedListsIds.includes(list.get('id')))

    return state.merge({
      searchBySourceListsAvailable: lists,
      searchBySourceLists: allLists
    })
  },

  [MOVE_SOURCE_LIST]: (state, { payload }) => {
    const listFrom = fromJS(state.get(payload.from))
    const filteredList = listFrom.filter((list) => {
      return list.get('id') !== payload.list.id
    })
    const listTo = state.get(payload.to).push(fromJS(payload.list))

    return state.merge({
      [payload.from]: filteredList,
      [payload.to]: listTo
    })
  },

  [TOGGLE_INCLUDE_DUPLICATES]: (state, {payload}) => {
    const includeDuplicates = !state.get('includeDuplicates')
    return state.set('includeDuplicates', includeDuplicates)
  },

  [TOGGLE_HAS_IMAGES]: (state, {payload}) => {
    const hasImages = !state.get('hasImages')
    return state.set('hasImages', hasImages)
  },

  [RENEW_SEARCH_BY]: (state) => {
    
    const {
      common: { auth }
    } = storeObj.getState().toJS();

    let chosenMediaTypes = [];

    if (
      auth &&
      auth.user &&
      auth.user.restrictions &&
      auth.user.restrictions.plans
    ) {
      const planDetails = auth.user.restrictions.plans;
      chosenMediaTypes = allMediaTypes.filter((v) => planDetails[v]);
      /* if (auth.user.restrictions.plans.price === 0) {
        // TODO: remove following restrictions when duplication fixes
        const restrictedTemporary = ['news', 'blogs'];
        chosenMediaTypes = chosenMediaTypes.filter(
          (v) => !restrictedTemporary.includes(v)
        );
      } */
    }
    
    return state.merge(initialState.toJS()).set('chosenMediaTypes', chosenMediaTypes)
  },

  [SET_COMMON_FILTERS]: (state, {payload}) => {
    const { filters, sourceLists, sources } = payload

    let result = initialState
      .merge({
        chosenSearchByTab: state.get('chosenSearchByTab'),
        isSearchByVisible: state.get('isSearchByVisible'),
        searchBySources: state.get('searchBySources'),
        searchBySourceLists: state.get('searchBySourceLists')
      })
      .toJS()

    if (filters.headline) {
      result.headlineIncluded = filters.headline.include || ''
      result.headlineExcluded = filters.headline.exclude || ''
    }

    const { source, domain } = filters.publisher || {}
    if (source || domain) {
      const medias = (source || []).concat(domain || []);
      result.chosenMediaTypes = medias.map((v) => v.split('.')[0]);
    }
    
    if (filters.source && sources.length > 0) {
      result.searchBySourcesType = filters.source.type
      result.selectedSearchBySources = sources
    }

    let usedSourceLists = []
    if (filters.sourceList) {
      if (filters.sourceList.include) {
        result.searchBySourceListsToInclude = sourceLists.filter(source => {
          return filters.sourceList.include.includes(source.id)
        })
        usedSourceLists = usedSourceLists.concat(filters.sourceList.include)
      }
      if (filters.sourceList.exclude) {
        result.searchBySourceListsToExclude = sourceLists.filter(source => {
          return filters.sourceList.exclude.includes(source.id)
        })
        usedSourceLists = usedSourceLists.concat(filters.sourceList.exclude)
      }
    }

    result.searchBySourceListsAvailable = result.searchBySourceLists.filter(source => {
      return !usedSourceLists.includes(source.id)
    })

    if (filters.language) {
      result.chosenLanguages = filters.language
    }

    if (filters.country || filters.state) {
      let locationsToInclude = []
      let locationsToExclude = []
      let locationsMain = []
      locations.forEach(location => {
        const code = location.code

        if (location.type === 'country') {
          if (filters.country && filters.country.include && filters.country.include.includes(code)) {
            locationsToInclude.push(location)
          }
          else if (filters.country && filters.country.exclude && filters.country.exclude.includes(code)) {
            locationsToExclude.push(location)
          }
          else {
            locationsMain.push(location)
          }
        }

        if (location.type === 'state') {
          if (filters.state && filters.state.include && filters.state.include.includes(code)) {
            locationsToInclude.push(location)
          }
          else if (filters.state && filters.state.exclude && filters.state.exclude.includes(code)) {
            locationsToExclude.push(location)
          }
          else {
            locationsMain.push(location)
          }
        }
      })

      result.locationsToInclude = locationsToInclude
      result.locationsToExclude = locationsToExclude
      result.locations = locationsMain
    }

    if (filters.date) {
      let dateResult = {}
      if (filters.date.type === 'all') {
        dateResult = {
          chosenSearchDate: 'all',
          chosenSearchInterval: 'all',
          chosenSearchLastDate: '',
          chosenStartDate: '',
          chosenEndDate: ''
        }
      }
      else if (filters.date.type === 'last') {
        dateResult = {
          chosenSearchDate: filters.date.days + 'd',
          chosenSearchInterval: 'last',
          chosenSearchLastDate: filters.date.days + 'd',
          chosenStartDate: '',
          chosenEndDate: ''
        }
      }
      else if (filters.date.type === 'between') {
        dateResult = {
          chosenSearchDate: `${filters.date.start} - ${filters.date.end}`,
          chosenSearchInterval: 'between',
          chosenSearchLastDate: '',
          chosenStartDate: filters.date.start,
          chosenEndDate: filters.date.end
        }
      }
      result = Object.assign(result, dateResult)
    }
    if (filters.duplicates) {
      result.includeDuplicates = filters.duplicates
    }
    if (filters.hasImage) {
      result.hasImages = filters.hasImage
    }

    return state.merge(result)
  }

}, initialState)
