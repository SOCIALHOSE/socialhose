import {createApi} from '../common/Common'

export const getCategories = createApi('GET', '/api/v1/categories')

//payload = {name, parent}
export const addCategory = createApi('POST', '/api/v1/categories', {
  urlData: (payload, feedId) => ({feedId})
})

//payload = {name, parent}
export const renameCategory = createApi('PUT', '/api/v1/categories/{categoryId}', {
  urlData: (payload, categoryId) => ({categoryId})
})

export const moveCategory = createApi('POST', '/api/v1/categories/{categoryId}/move_to/{newCategoryId}', {
  urlData: (payload, categoryId, newCategoryId) => ({categoryId, newCategoryId})
})

//payload = {name, parent}
export const deleteCategory = createApi('DELETE', '/api/v1/categories/{categoryId}', {
  urlData: (payload, categoryId) => ({categoryId})
})
