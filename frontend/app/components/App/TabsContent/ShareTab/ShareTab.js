import React from 'react';
import PropTypes from 'prop-types';
import { Redirect, Route, Switch, withRouter } from 'react-router-dom';
import SubTabWrapper from '../../AppHeader/SubTabWrapper';
import CSSTransitionGroup from 'react-transition-group/CSSTransitionGroup';
import NotificationsSubTab from './NotificatoinsSubTab/NotificationsSubTab';
import ManageRecipientsSubTab from './ManageRecipientsSubTub/ManageRecipientsSubTab';
import ManageEmailsSubTab from './ManageEmailsSubTab/ManageEmailsSubTab';
import ExportSubTab from './ExportSubTab/ExportSubTab';

class ShareTab extends React.Component {
  static propTypes = {
    activeTabName: PropTypes.string,
    subTabs: PropTypes.any,
    match: PropTypes.object,
    isMaster: PropTypes.bool
  };

  render() {
    const { subTabs, isMaster, match, activeTabName } = this.props;
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
            <Route
              exact
              path={`${match.url}/notifications`}
              component={NotificationsSubTab}
            />
            <Route
              exact
              path={`${match.url}/export`}
              component={ExportSubTab}
            />
            {isMaster
              ? [
                <Route
                  exact
                  key={`${match.url}/manage-recipients`}
                  path={`${match.url}/manage-recipients`}
                  component={ManageRecipientsSubTab}
                />,
                <Route
                  exact
                  key={`${match.url}/manage-emails`}
                  path={`${match.url}/manage-emails`}
                  component={ManageEmailsSubTab}
                />
              ]
              : null}
            <Redirect to={`${match.url}/notifications`} />
          </Switch>
        </SubTabWrapper>
      </CSSTransitionGroup>
    );
  }
}

export default withRouter(ShareTab);
