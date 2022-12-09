import { applyMiddleware, compose, createStore } from 'redux';
import thunk from 'redux-thunk';
import { routerMiddleware } from 'react-router-redux';
import { history } from '../main';

export const hasBrowserExt = window.__REDUX_DEVTOOLS_EXTENSION__;

export default function configureStore(initialState, rootReducer) {
  const middleware = applyMiddleware(thunk, routerMiddleware(history));

  let createStoreWithMiddleware;

  if (process.env.NODE_ENV === 'development') {
    if (hasBrowserExt) {
      // show browser devtools if available
      const storeEnhancers =
        (typeof window === 'object' &&
          window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__) ||
        compose;

      createStoreWithMiddleware = storeEnhancers(middleware);
    } else {
      createStoreWithMiddleware = compose(
        middleware,
        require('./utils/DevTools').default.instrument()
      );
    }
  } else {
    createStoreWithMiddleware = compose(middleware);
  }

  const store = createStoreWithMiddleware(createStore)(
    rootReducer,
    initialState
  );

  return store;
}
