import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Link, withRouter } from 'react-router-dom';
import { Nav, NavItem } from 'reactstrap';
import cl from 'classnames';

export class MainTabsLinks extends React.Component {
  static propTypes = {
    tabs: PropTypes.object.isRequired,
    restrictions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired,
    location: PropTypes.object
  };

  validateTab = (tab) => {
    if (tab === 'analyze') {
      if (!this.props.restrictions) {
        // to prevent: permissions of `undefined`
        return false;
      }
      const permissions = this.props.restrictions.permissions;
      return permissions.analytics;
    }
    return true;
  };

  showUpgradeModal = (e) => {
    e.preventDefault();
    this.props.actions.toggleUpgradeModal();
  };

  render() {
    const { t, tabs, location } = this.props;

    return (
      <Nav className="header-megamenu">
        {Object.keys(tabs).map((tab, i) => {
          const firstSubTab =
            tabs[tab].items && tabs[tab].items[0] ? tabs[tab].items[0].url : '';

          if (!this.validateTab(tab)) {
            return (
              <NavItem key={tab}>
                <a
                  href="#"
                  onClick={this.showUpgradeModal}
                  className={cl('nav-link', {
                    active: location.pathname.startsWith(`/app/${tab}`)
                  })}
                >
                  <i className={`nav-link-icon ${tabs[tab].icon}`}> </i>
                  <p>{t('tabs.' + tab)}</p>
                </a>
              </NavItem>
            );
          }
          return (
            <NavItem key={tab}>
              <Link
                to={`/app/${tab}/${firstSubTab}`}
                className={cl('nav-link', {
                  active: location.pathname.startsWith(`/app/${tab}`)
                })}
              >
                <i className={`nav-link-icon ${tabs[tab].icon}`}> </i>
                <p>{t('tabs.' + tab)}</p>
              </Link>
            </NavItem>
          );
        })}
      </Nav>
    );
  }
}

export default translate(['common'], { wait: true })(
  withRouter(React.memo(MainTabsLinks))
);
