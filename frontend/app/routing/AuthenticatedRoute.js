import React, { Fragment, useEffect } from 'react';
import PropTypes from 'prop-types';
import { get } from 'lodash';
import reduxConnect from '../redux/utils/connect';
import { Route, Redirect, Switch } from 'react-router-dom';
import App from '../components/App/App';
import SearchTab from '../components/App/TabsContent/SearchTab/SearchTab';
import ShareTab from '../components/App/TabsContent/ShareTab/ShareTab';
import AnalyzeTab from '../components/App/TabsContent/AnalyzeNewTab/AnalyzeTab';
import UserPlans from '../components/App/Account/Plans/UserPlans';
import UpgradePlanModal from '../components/App/Account/Plans/UpgradePlanModal';

function AuthenticatedRoute(props) {
  const {
    common: { base, auth },
    match,
    history,
    actions
  } = props;
  const { isAuthPending, token: isLoggedIn, user } = auth;

  const isMaster = user && user.role === 'ROLE_MASTER_USER';
  const activeTab = match.params && match.params.activeTab;

  const allowAnalytics = get(user, [
    'restrictions',
    'permissions',
    'analytics'
  ]);

  useEffect(() => {
    if (!isAuthPending && !isLoggedIn) {
      history.push('/auth/login');
      return;
    }
  }, [isAuthPending, isLoggedIn]);

  const activeTabDetails = base.tabs[activeTab];
  let subTabs = activeTabDetails && activeTabDetails.items;

  if (subTabs) {
    subTabs = subTabs.filter((tab) => {
      return !tab.masterOnly || auth.user.role === 'ROLE_MASTER_USER';
    });
  }

  if (!isAuthPending && !isLoggedIn) {
    return null;
  }

  return (
    <App>
      <Fragment>
        <Switch>
          <Route path="/app/search">
            <SearchTab activeTabName={activeTab} subTabs={subTabs} />
          </Route>
          <Route path="/app/analyze">
            <AnalyzeTab
              activeTabName={activeTab}
              subTabs={subTabs}
              allowAnalytics={allowAnalytics}
            />
          </Route>
          <Route path="/app/share">
            <ShareTab
              activeTabName={activeTab}
              subTabs={subTabs}
              isMaster={isMaster}
            />
          </Route>
          <Route path="/app/plans">
            <UserPlans />
          </Route>
          <Redirect to="/app/search/search" />
        </Switch>

        <UpgradePlanModal
          isModalOpen={base.isUpgradeVisible}
          toggle={actions.toggleUpgradeModal}
        />
      </Fragment>
    </App>
  );
}

AuthenticatedRoute.propTypes = {
  actions: PropTypes.object.isRequired,
  common: PropTypes.object.isRequired,
  match: PropTypes.object.isRequired,
  history: PropTypes.object.isRequired
};

export default reduxConnect('common', ['common'])(AuthenticatedRoute);
