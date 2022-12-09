import {createApi} from '../common/Common'

const baseUrl = '/api/v1/recipients/groups'

export const getItems = createApi('GET', baseUrl, {
  inputData: (data) => data
})

export const createItem = createApi('POST', baseUrl)

export const updateItem = createApi('PUT', baseUrl + '/{groupId}', {
  urlData: (data, groupId) => ({groupId})
})

export const deleteItems = createApi('POST', baseUrl + '/delete')

export const activateItems = createApi('PUT', baseUrl + '/active')
