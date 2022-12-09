import {createApi} from '../common/Common'

const baseUrl = '/api/v1/notifications/themes'

export const getDefaultItem = createApi('GET', baseUrl + '/default', {
  inputData: (data) => data
})
