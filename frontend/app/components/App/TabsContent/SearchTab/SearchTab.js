import React from 'react';
import PropTypes from 'prop-types';
import { Redirect, Route, Switch, withRouter } from 'react-router-dom';
import SubTabWrapper from '../../AppHeader/SubTabWrapper';
import CSSTransitionGroup from 'react-transition-group/CSSTransitionGroup';
import SearchSubTab from './SearchSubTab/SearchSubTab';
import SourceIndexSubTab from './SourceIndexSubTab/SourceIndexSubTab';
import SourceListsSubTab from './SourceListsSubTab/SourceListsSubTab';

class SearchTab extends React.Component {
  static propTypes = {
    activeTabName: PropTypes.string,
    match: PropTypes.object,
    subTabs: PropTypes.array
  };

  render() {
    const { activeTabName, subTabs, match } = this.props;
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
            <Route path={`${match.url}/search`} component={SearchSubTab} />
            <Route
              path={`${match.url}/source-index`}
              component={SourceIndexSubTab}
            />
            <Route
              path={`${match.url}/source-lists`}
              component={SourceListsSubTab}
            />
            <Redirect to={`${match.url}/search`} />
          </Switch>
        </SubTabWrapper>
      </CSSTransitionGroup>
    );
  }
}

export default withRouter(SearchTab);
