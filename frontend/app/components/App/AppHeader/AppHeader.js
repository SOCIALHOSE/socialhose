import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import HeaderSettings from './HeaderSettings';
import SettingsPopup from './SettingsPopup';
import cx from 'classnames';
import MainTabsLinks from './MainTabsLinks';
import CSSTransitionGroup from 'react-transition-group/CSSTransitionGroup';
import HeaderLogo from './HeaderLogo';
import HeaderDots from './HeaderDots';

export class AppHeader extends React.Component {
  static propTypes = {
    appCommonState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    userFirstName: PropTypes.string,
    userLastName: PropTypes.string,
    userRole: PropTypes.string.isRequired,
    restrictions: PropTypes.object.isRequired,
    themeOptions: PropTypes.object.isRequired
  };

  state = {
    active: false,
    mobile: false,
    activeSecondaryMenuMobile: false
  };

  toggleResponsiveMenu = () => {
    this.props.actions.toggleSidebar();
  };

  activeSearchFunc = () => {
    this.setState({ active: !this.state.active });
  };

  render() {
    const {
      appCommonState,
      restrictions,
      actions,
      userFirstName,
      userLastName,
      themeOptions
    } = this.props;
    const mainTabs = Object.keys(appCommonState.tabs);

    const {
      headerBackgroundColor,
      enableHeaderShadow,
      enableMobileMenuSmall
    } = themeOptions;

    const settingsPopupVisible = appCommonState.isSettingsPopupVisible;

    return (
      <Fragment>
        <CSSTransitionGroup
          component="div"
          className={cx('app-header', headerBackgroundColor, {
            'header-shadow': enableHeaderShadow
          })}
          transitionName="HeaderAnimation"
          transitionAppear
          transitionAppearTimeout={1500}
          transitionEnter={false}
          transitionLeave={false}
        >
          <HeaderLogo />
          <div
            className={cx('app-header__content', {
              'header-mobile-open': enableMobileMenuSmall
            })}
          >
            <div className="app-header-left" data-tour="app-header-left">
              <MainTabsLinks
                tabs={appCommonState.tabs}
                restrictions={restrictions}
                actions={actions}
              />
            </div>
            <div className="app-header-right">
              <HeaderDots
                mainTabs={mainTabs}
                restrictions={restrictions}
                planDetails={restrictions.plans}
              />
              <HeaderSettings
                isThereSomethingNew={appCommonState.isThereSomethingNew}
                langs={appCommonState.langs}
                userFirstName={userFirstName}
                userLastName={userLastName}
              />
            </div>
          </div>

          {settingsPopupVisible && (
            <SettingsPopup
              hidePopup={actions.hideUserSettingsPopup}
              setErrorMsg={actions.setSettingsPopupError}
              changePassword={actions.changeUserPassword}
              errorMsg={appCommonState.settingsPopupError}
            />
          )}
        </CSSTransitionGroup>
      </Fragment>
    );
  }
}

export default AppHeader;
