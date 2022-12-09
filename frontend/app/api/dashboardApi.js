import {createApi, mockApi} from '../common/Common'

const base = '/api/v1/dashboards'

/*class DashboardWidget {
  id: number,
  type: "feed" | "chart" | "video" | "youtube",
  name?: string,
  source?: Feed | Chart,
  limit?: number,
  url?: string
}

class Dashboard {
  id: ...
  name: string,
  layout: any,
  widgets: DashboardWidget[]
}
*/

//export const getDashboards = createApi('GET', base);
export const getDashboards = mockApi([
  {id: 1, name: 'My Dashboard', layout: '{ver: 1, left: [1, 2], right: [3, 4]}', widgets: [
    {id: 1, type: 'feed', name: 'Widget1', source: {id: 1}, limit: 5},
    {id: 2, type: 'feed', name: 'Widget2', source: {id: 2}, limit: 5},
    {id: 3, type: 'feed', name: 'Widget3', source: {id: 3}, limit: 5},
    {id: 4, type: 'feed', name: 'Widget4', source: {id: 4}, limit: 5}
  ]},
  {id: 2, name: 'Dashboard 2', layout: '{ver: 1, left: [5, 6, 7], right: [8]}', widgets: [
    {id: 5, type: 'feed', name: 'Widget5', source: {id: 1}, limit: 5},
    {id: 6, type: 'feed', name: 'Widget6', source: {id: 2}, limit: 5},
    {id: 7, type: 'feed', name: 'Widget7', source: {id: 3}, limit: 5},
    {id: 8, type: 'feed', name: 'Widget8', source: {id: 4}, limit: 5}
  ]},
  {id: 44, name: 'Not_my_dashboard', layout: '{ver: 1, left: [], right: [9, 10, 11, 12]}', widgets: [
    {id: 9, type: 'feed', name: 'Widget9', source: {id: 1}, limit: 5},
    {id: 10, type: 'feed', name: 'Widget10', source: {id: 2}, limit: 5},
    {id: 11, type: 'feed', name: 'Widget11', source: {id: 3}, limit: 5},
    {id: 12, type: 'feed', name: 'Widget12', source: {id: 4}, limit: 5}
  ]}
])

//payload = {name}
export const createDashboard = createApi('POST', base)

//payload = dashboard widget
export const createDashboardWidget = createApi('POST', `${base}/{dashboardId}/widgets`, {
  urlData: (payload, dashboardId) => ({dashboardId})
})

export const getVideoWidgetUrl = createApi('GET', `${base}/{dashboardId}/widgets/{widgetId}/video`, {
  urlData: (payload, dashboardId, widgetId) => ({dashboardId, widgetId})
})

//payload = dashboard without widgets
export const updateDashboard = createApi('PUT', `${base}/{dashboardId}`, {
  urlData: (payload, dashboardId) => ({dashboardId})
})

//payload = dashboard widget
export const updateDashboardWidget = createApi('PUT', `${base}/{dashboardId}/widgets/{widgetId}`, {
  urlData: (payload, dashboardId, widgetId) => ({dashboardId, widgetId})
})

export const deleteDashboardWidget = createApi('DELETE', `${base}/{dashboardId}`)

export const deleteDashboard = createApi('DELETE', `${base}/{dashboardId}`)
