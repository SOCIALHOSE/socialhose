import {createApi} from '../common/Common'

const baseUrl = '/api/v1/receivers'

export const getItems = createApi('GET', baseUrl, {
  inputData: (data) => data
})

export const getEmailHistory = createApi('GET', baseUrl + '/{id}/emailHistory', {
  urlData: (payload, receiverId) => ({id: receiverId}),
  inputData: data => data
})
