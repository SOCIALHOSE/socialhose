import {createApi} from '../common/Common'

const baseUrl = '/api/v1/notifications'

export const getItems = createApi('GET', baseUrl, {
  inputData: (data) => data
})

export const getItem = createApi('GET', baseUrl + '/{id}', {
  inputData: () => {},
  urlData: (data, id) => ({id})
})

export const createItem = createApi('POST', baseUrl)

export const updateItem = createApi('PUT', baseUrl + '/{id}', {
  urlData: (data, id) => ({id})
})

export const deleteItems = createApi('POST', baseUrl + '/delete')

export const activateItems = createApi('PUT', baseUrl + '/active')

export const publishItems = createApi('PUT', baseUrl + '/published')

export const subscribeItems = createApi('POST', baseUrl + '/subscribe')

export const getAllItems = createApi('GET', baseUrl + '/all', {
  inputData: (data) => data
})
export const getFilters = createApi('GET', baseUrl + '/filters', {
  inputData: (data) => data
})

export const getHistory = createApi('GET', baseUrl + '/{notificationId}/history', {
  inputData: (data) => data,
  urlData: (data, notificationId) => ({notificationId})
})
