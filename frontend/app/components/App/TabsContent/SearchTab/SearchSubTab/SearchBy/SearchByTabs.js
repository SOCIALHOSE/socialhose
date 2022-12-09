import React from 'react';
import PropTypes from 'prop-types';
import { Nav, NavLink, NavItem } from 'reactstrap';
import { translate } from 'react-i18next';

export class SearchByTabs extends React.Component {
  static propTypes = {
    searchByTabs: PropTypes.array.isRequired,
    chosenSearchByTab: PropTypes.string.isRequired,
    chooseSearchByTab: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  chooseSearchByTab = (newTab) => () => {
    this.props.chooseSearchByTab(newTab);
  };

  render() {
    const { searchByTabs } = this.props;
    const { t } = this.props;

    return (
      <Nav tabs className="font-size-xs">
        {searchByTabs.map((tab, i) => (
          <NavItem key={tab}>
            <NavLink
              className="d-block"
              active={tab === this.props.chosenSearchByTab}
              onClick={this.chooseSearchByTab(tab)}
            >
              {t('searchTab.searchBySection.' + tab + '.title')}
            </NavLink>
          </NavItem>
        ))}
      </Nav>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(SearchByTabs);
