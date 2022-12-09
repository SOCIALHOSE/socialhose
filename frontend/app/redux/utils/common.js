import {createAction} from 'redux-actions'
import {addAlert} from '../modules/common/alerts'

export const tokenInject = (fn) =>
  (dispatch, getState) =>
    fn(dispatch, getState, getState().getIn(['common', 'auth', 'token']))

export const thunkAction = (actionName, actionMethod, emitPending = false, customPendingAction = false) => {
  const fulfilledAction = createAction(`${actionName} fulfilled`)
  const pendingAction = customPendingAction ||
    createAction(`${actionName} pending`, (isPending, success) => ({isPending, success}))

  return (...args) => {
    return tokenInject((dispatch, getState, token) => {

      const fulfilled = (...fArgs) => {
        dispatch(fulfilledAction(...fArgs))
        emitPending && dispatch(pendingAction(false, true))
      }

      const onError = (errors) => {
        dispatch(addAlert(errors))
        emitPending && dispatch(pendingAction(false, false))
      }

      emitPending && dispatch(pendingAction(true))

      let result
      try {
        result = actionMethod(...args, {dispatch, getState, token, fulfilled})
      } catch (e) {
        console.error('Error in thunkAction()')
        console.error(e)
        throw e
      }
      if (result instanceof Promise) {
        result.catch(onError)
      }
      return result
    })
  }
}

export const routerSelectLocationState = (state) => {
  return state.get('routing').toJS()
}
