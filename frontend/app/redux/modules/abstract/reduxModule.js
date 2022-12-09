import { createAction, handleActions } from 'redux-actions'
import { fromJS } from 'immutable'
import {thunkAction} from '../../utils/common'

export class ReduxModule {

  constructor () {
    this._namespace = this.getNamespace()
    this._actions = {}
    this._reducers = {}
  }

  init () {
    this._initialState = fromJS(this.getInitialState())
    const actions = this.defineActions()
    const reducers = this._addNamespaceToReducers(this.defineReducers())
    Object.assign(this._actions, actions)
    Object.assign(this._reducers, reducers)
    this.reducers = handleActions(this._reducers, this._initialState)
    this.actions = this._actions
  }

  getNamespace () {
    //implement in subclasses
  }

  getInitialState () {
    //implement in subclasses
  }

  defineActions () {
    //implement in subclasses
    //it should return hash of function
    /**
     * return {
     * action1,
     * action2
     * }
     */
  }

  defineReducers () {
    //implement in subclasses
    /*
    return {
    [actionName]: this.****Reducer()
    }
    or use this.addReducer()
     */
  }

  _addNamespaceToReducers (obj) {
    const result = {}
    for (let actionName in obj) {
      result[this.ns(actionName)] = obj[actionName]
    }
    return result
  }

  /** utils **/
  ns (actionName) {
    return `${this._namespace} ${actionName}`
  }

  evalPath (path) {
    return path
    //TODO: something better
    /*return path.map(
      (item) => (typeof item === 'function') ? item(state) : item
    );*/
  }

  /** action creators **/
  createAction (actionName, actionFn) {
    return createAction(this.ns(actionName), actionFn)
  }

  thunkAction (actionName, actionMethod, emitPending) {
    return thunkAction(this.ns(actionName), actionMethod, emitPending)
  }

  thunkPendingReducer (field) {
    return (state, {payload: {isPending}}) => state.set(field, isPending)
  }

  /** reducer creators **/
  setReducer (field) {
    return (state, {payload: value}) => state.set(field, value)
  }

  setInReducer (path) {
    return (state, {payload: value}) => state.setIn(this.evalPath(path), value)
  }

  setFieldReducer () {
    return (state, {payload: {field, value}}) => state.set(field, value)
  }

  resetReducer (field, defaultValue) {
    return (state) => state.set(field, defaultValue)
  }

  mergeReducer () {
    return (state, {payload: values}) => state.merge(values)
  }

  mergeInReducer (path) {
    return (state, {payload: values}) => state.mergeIn(this.evalPath(path), values)
  }

  toggleReducer (field) {
    return (state) => state.set(field, !state.get(field))
  }

  toggleInReducer (path) {
    return (state) => {
      const realPath = this.evalPath(path)
      return state.setIn(realPath, !state.getIn(realPath))
    }
  }

  addReducer (actionName, reducerFn) {
    this._reducers[this.ns(actionName)] = reducerFn
  }

  //do not prefix with namespace
  addExternalReducer (actionName, reducerFn) {
    this._reducers[actionName] = reducerFn
  }

  /** handler creators
   * handler = action + reducer
   **/
  createHandler (actionName, actionFn, reducerFn) {
    const action = this.createAction(actionName, actionFn)
    this.addReducer(actionName, reducerFn)
    return action
  }

  set (actionName, field) {
    return this.createHandler(actionName, {}, this.setReducer(field))
  }

  setIn (actionName, path) {
    return this.createHandler(actionName, {}, this.setInReducer(path))
  }

  setField (actionName) {
    return this.createHandler(actionName, (field, value) => ({field, value}), this.setFieldReducer())
  }

  reset (actionName, field, defaultValue) {
    return this.createHandler(actionName, {}, this.resetReducer(field, defaultValue))
  }

  merge (actionName) {
    return this.createHandler(actionName, {}, this.mergeReducer())
  }

  mergeIn (actionName, path) {
    return this.createHandler(actionName, {}, this.mergeInReducer(path))
  }

  toggle (actionName, field) {
    return this.createHandler(actionName, {}, this.toggleReducer(field))
  }

  toggleIn (actionName, path) {
    return this.createHandler(actionName, {}, this.toggleInReducer(path))
  }

  resetToInitialState (actionName) {
    return this.createHandler(actionName, {}, () => this._initialState)
  }

}

export default ReduxModule
