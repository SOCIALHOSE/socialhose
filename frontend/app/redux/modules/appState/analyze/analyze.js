import { fromJS } from 'immutable'
import { createAction, handleActions } from 'redux-actions'

// Action types
const ADD_ALERT_CHART = 'ADD_ALERT_CHART'
const REMOVE_ALERT_CHART = 'REMOVE_ALERT_CHART'
const RESET_ALERT_CHART = 'RESET_ALERT_CHART'

// Actions
const addAlertChart = createAction(ADD_ALERT_CHART, (payload) => payload)
const removeAlertChart = createAction(REMOVE_ALERT_CHART, (payload) => payload)
const resetAlertChart = createAction(RESET_ALERT_CHART, (payload) => payload)

export const analyzeActions = {
  addAlertChart,
  removeAlertChart,
  resetAlertChart
}

// Reducer
const initialState = fromJS({
  alertCharts: []
})

export default handleActions(
  {
    [ADD_ALERT_CHART]: (state, { payload }) => {
      const charts = state.getIn(['alertCharts'])
      if (charts.find((v) => v.name === payload)) {
        return state
      }
      return state.setIn(['alertCharts'], [...charts, payload])
    },
    [REMOVE_ALERT_CHART]: (state, { payload }) => {
      const charts = state
        .getIn(['alertCharts'])
        .filter(
          (item) =>
            item.name !== payload.name ||
            (item.id ? item.id !== payload.id : true)
        )
      return state.setIn(['alertCharts'], charts)
    },
    [RESET_ALERT_CHART]: (state) => {
      return state.setIn(['alertCharts'], [])
    }
  },
  initialState
)
