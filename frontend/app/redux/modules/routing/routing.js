import { handleActions } from 'redux-actions'
import { fromJS } from 'immutable'
import { LOCATION_CHANGE } from 'react-router-redux'

export const initialState = fromJS({
  locationBeforeTransitions: null
})

export default handleActions({
  [LOCATION_CHANGE]: (state, {payload}) => {
    return state.set('locationBeforeTransitions', payload)
  }
}, initialState)
