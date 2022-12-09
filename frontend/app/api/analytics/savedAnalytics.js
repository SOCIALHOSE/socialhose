import { get, del } from '../httpInterceptor/httpInterceptor'

export const savedAnalytics = async (params) => {
  let url = '/analysis'
  const res = await get(url, params)
  console.log('API Response :: savedAnalytics ::: ', res)
  return res
}

export const deleteAnalytics = async (id) => {
  let url = `/analysis/${id}`
  const res = await del(url)
  console.log('API Response :: deleteAnalytics ::: ', res)
  return res
}
