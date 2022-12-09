import {createApi} from '../common/Common'

const root = '/api/v1/feed'

/**
 * payload: {feed: {name: string, category: id, subType: string}, search: {query: string, filters: Object, advancedFilters: Object}}
 */
export const createFeed = createApi('POST', root)

/**
 * payload: {feed: {name: string, category: id, subType: string}, search: {query: string, filters: Object, advancedFilters: Object}}
 */
export const saveFeed = createApi('PUT', `${root}/{feedId}`, {
  urlData: (data, feedId) => ({feedId})
})

/**
 * payload = {name: string}
 */
export const renameFeed = createApi('PUT', `${root}/{feedId}/rename`, {
  urlData: (payload, feedId) => ({feedId})
})

export const moveFeed = createApi('POST', `${root}/{feedId}/move_to/{categoryId}`, {
  urlData: (payload, feedId, categoryId) => ({feedId, categoryId})
})

export const deleteFeed = createApi('DELETE', `${root}/{feedId}`, {
  urlData: (payload, feedId) => ({feedId})
})

/**
 * payload: {page: number, advancedFilters: Object}
 */
export const getFeedSearchResults = createApi('POST', `${root}/{feedId}/documents`, {
  urlData: (params, feedId) => ({feedId})
})

/**
 * payload = {export: bool}
 */
export const toggleExportFeed = createApi('PUT', `${root}/{feedId}/toggleExport`, {
  urlData: (payload, feedId) => ({feedId})
})

/**
 * payload = {export: bool}
 */
export const toggleExportCategory = createApi('PUT', `${root}/toggleExport/{categoryId}`, {
  urlData: (payload, categoryId) => ({categoryId})
})

export const loadExportedFeeds = createApi('GET', `${root}/exported`)

