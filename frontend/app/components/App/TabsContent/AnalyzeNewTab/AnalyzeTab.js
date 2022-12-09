import React from 'react';
import PropTypes from 'prop-types';
import CSSTransitionGroup from 'react-transition-group/CSSTransitionGroup';
import SubTabWrapper from '../../AppHeader/SubTabWrapper';
import { Redirect, Route, Switch, withRouter } from 'react-router-dom';
import ShowCharts from './CreateAnalysisSubTab/ShowCharts';
import SavedAnalysisSubTab from './SavedAnalysisSubTab/SavedAnalysisSubTab';
import CreateAnalysisSubTab from './CreateAnalysisSubTab/CreateAnalysisSubTab';

function AnalyzeTab(props) {
  const { subTabs, allowAnalytics, history, activeTabName, match } = props;

  if (!allowAnalytics) {
    history.push('/app/search/search');
    return null;
  }

  return (
    <CSSTransitionGroup
      component="div"
      transitionName="TabsAnimation"
      transitionAppear
      transitionAppearTimeout={0}
      transitionEnter={false}
      transitionLeave={false}
    >
      <SubTabWrapper activeTabName={activeTabName} subTabs={subTabs}>
        <Switch>
          {/* <Route path={`${match.url}/welcome`} component={WelcomeSubTab} /> */}
          <Route path={`${match.url}/saved`} component={SavedAnalysisSubTab} />
          <Route
            path={`${match.url}/create`}
            component={CreateAnalysisSubTab}
          />
          <Route
            path={`${match.url}/edit/:id`}
            component={CreateAnalysisSubTab}
          />
          <Route path={`${match.url}/:id`} component={ShowCharts} />
          <Redirect to={`${match.url}/saved`} />
        </Switch>
      </SubTabWrapper>
    </CSSTransitionGroup>
  );
}

AnalyzeTab.propTypes = {
  activeTabName: PropTypes.string,
  children: PropTypes.any,
  history: PropTypes.object,
  match: PropTypes.object,
  allowAnalytics: PropTypes.bool,
  subTabs: PropTypes.array
};

export default withRouter(AnalyzeTab);
