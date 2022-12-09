import React, { Fragment } from 'react';
import ReactDOM from 'react-dom';
import { fromJS } from 'immutable';
import { Provider } from 'react-redux';
import { createBrowserHistory as createHistory } from 'history';
import { Router } from 'react-router-dom';
import { I18nextProvider } from 'react-i18next';
import { rootReducers } from './redux/root';

import i18n from './i18n';
import DevTools from './redux/utils/DevTools';
import configureStore, { hasBrowserExt } from './redux/configureStore';
import { Elements } from '@stripe/react-stripe-js';
import { loadStripe } from '@stripe/stripe-js';

// import { syncHistoryWithStore } from 'react-router-redux';
// import { routerSelectLocationState } from './redux/utils/common';
// import configureRoutes from './routing/routes';

import 'react-select/dist/react-select.css';
import './styles/core.scss';

import AppRoutes from './routing/AppRoutes'; // keep after loading css
import SiteScripts from './routing/SiteScripts';
import { isLocal, isProduction } from './common/constants';
import appConfig from './appConfig';

export const history = createHistory();

const stripePromise = loadStripe(appConfig.stripeKey);

export const storeObj = configureStore(fromJS({}), rootReducers);

export const init = function (options) {
  if (!options.containerId) {
    console.error('There are no containerId');
    return false;
  }

  const store = (this.store = storeObj);
  // const routes = configureRoutes(store);
  // const history = syncHistoryWithStore(browserHistory, store, {
  //   selectLocationState: routerSelectLocationState
  // });

  ReactDOM.render(
    <Fragment>
      {isProduction && <SiteScripts />}
      <I18nextProvider i18n={i18n}>
        <Provider store={store}>
          <Elements stripe={stripePromise}>
            <Router history={history}>
              <AppRoutes />
            </Router>
            {isLocal && !hasBrowserExt && <DevTools />}
          </Elements>
        </Provider>
      </I18nextProvider>
    </Fragment>,
    document.getElementById(options.containerId)
  );
};
