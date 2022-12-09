import { sum } from 'lodash'
import { convertlocaltoUTC } from '../../common/helper'
import { get, post, put } from '../httpInterceptor/httpInterceptor'

const formatValues = (data) => {
  let newObj = {}
  for (const [key, value] of Object.entries(data)) {
    for (let v in value) {
      newObj[v] = newObj[v]
        ? { ...newObj[v], [key]: value[v] }
        : { [key]: value[v] }
    }
  }

  Object.keys(data).map((dt) => {
    for (let item in newObj) {
      newObj[item][dt] = newObj[item][dt] || 0
    }
  })

  const obj = Object.keys(newObj).map((v) => ({
    name: v,
    data: newObj[v]
  }))

  return obj
}

export const addEditAnalyticsAPI = async (data, id) => {
  let url = `/analysis${id ? `/${id}` : ''}`

  const bodyObj = {}
  // bodyObj.filters = [] // need changes
  bodyObj.filters = {
    date: {
      type: 'between',
      start: convertlocaltoUTC(data.startDate, 'YYYY-MM-DD'),
      end: convertlocaltoUTC(data.endDate, 'YYYY-MM-DD')
    }
  }
  bodyObj.feeds = data.feeds.map((val) => val.id)

  const func = id ? put : post
  const res = await func(url, bodyObj)
  console.log('API Response :: addEditAnalytics ::: ', res)
  return res
}

export const getAnalyticDetailsAPI = async (id) => {
  let url = `/analysis/${id}`
  const res = await get(url)
  console.log('API Response :: getAnalyticDetails ::: ', res)
  return res
}

export const createAlertAPI = async (data) => {
  let url = '/notifications'
  const res = await post(url, data)
  console.log('API Response :: creteAnalytics ::: ', res)
  return res
}

/* Chart APIs */
export const getOverviewBarAPI = async (type = 'none', id) => {
  let isOther = type !== 'none'
  let url = isOther
    ? `/mention-over-time-bar-graph/${id}`
    : `/mention-bar-graph/${id}`
  const res = await post(url, isOther ? { type } : undefined)
  if (isOther && res.data && res.data.data) {
    res.data.data = res.data.data.map((feed) => ({
      name: feed.name,
      data: formatValues(feed.data)
    }))
  }
  console.log('API Response :: getOverviewBarAPI ::: ', res)
  return res
}

/* Used for Overview, Performance, Sentiment, Demographics */
export const getOverviewPieAPI = async (type = 'none', id) => {
  let isOther = type !== 'none'
  let url = isOther
    ? `/mention-over-time-pie-graph/${id}`
    : `/mention-pie-graph/${id}`
  const res = await post(url, isOther ? { type } : undefined)
  console.log('API Response :: getOverviewPieAPI ::: ', res)
  return res
}

export const getInfluencersAPI = async (id, filter, data = undefined) => {
  let url = `/influencer/${id}`
  if (filter === 1) {
    data = { isAuthorType: true }
  }
  const res = await post(url, data)
  console.log('API Response :: getInfluencersAPI ::: ', res)
  return res
}

export const getEngagementsTimeAPI = async (id) => {
  let url = `/engagement-over-time-bar-graph/${id}`
  const res = await post(url)
  console.log('API Response :: getEngagementsTimeAPI ::: ', res)
  return res
}

export const getEngagementsAPI = async (id) => {
  let url = `/engagement-over-time-pie-graph/${id}`
  const res = await post(url)
  console.log('API Response :: getEngagementsAPI ::: ', res)
  return res
}

/* Themes */

export const getThemesTimeAPI = async (id) => {
  let url = `/theme-over-time-bar-graph/${id}`
  const res = await post(url)
  const { data } = res.data
  let newData = data
  if (data) {
    newData = data.map((feedData) => {
      const { name, data } = feedData
      let dataTotal = data.map((theme) => {
        const { name, data } = theme
        const total = sum(Object.values(data))
        return { name, data, total }
      })
      dataTotal = topN(dataTotal, 5)
      return { name, data: dataTotal }
    })
  }
  res.data.data = newData
  console.log('API Response :: getThemesTimeAPI ::: ', res)
  return res
}

function topN(arr, n) {
  if (n > arr.length) {
    return arr
  }
  return arr
    .slice()
    .sort((a, b) => {
      return b.total - a.total
    })
    .slice(0, n)
}

export const getThemesCloudAPI = async (id) => {
  let url = `/theme-over-time-pie-graph/${id}`
  const res = await post(url)
  console.log('API Response :: getThemesCloudAPI ::: ', res)
  return res
}

/* World Map */

export const getWorldMapAPI = async (id) => {
  let url = `/world-map/${id}`
  const res = await post(url)
  console.log('API Response :: getWorldMapAPI ::: ', res)
  return res
}
