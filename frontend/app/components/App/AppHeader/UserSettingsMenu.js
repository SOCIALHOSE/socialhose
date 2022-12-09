/* eslint-disable react/jsx-no-bind */
import React from 'react';
import PropTypes from 'prop-types';
import { compose } from 'redux';
import { translate } from 'react-i18next';
import { Nav, Button, NavItem, NavLink } from 'reactstrap';
import PerfectScrollbar from 'react-perfect-scrollbar';
import city from '../../../images/city3.jpg';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faUser } from '@fortawesome/free-solid-svg-icons';
import { reduxActions } from '../../../redux/utils/connect';
import tourPages from './WebTourSteps';

import { useHistory } from 'react-router';
import { planRoutes } from '../Account/Plans/UserPlans';

function UserSettingsMenu(props) {
  const { push } = useHistory();

  function hideMenu() {
    props.toggleMenu();
    props.actions.setEnableMobileMenuSmall(false);
  }

  function showUserSettings() {
    hideMenu();
    props.actions.showUserSettingsPopup();
  }

  function onLogout() {
    hideMenu();
    props.actions.logout();
  }

  function tourGuide(path) {
    const win = window.open(`${path}?webtour=true`, '_blank');
    win.focus();

    // props.actions.toggleWebTour(); for dev
  }

  function gotToActivePlan() {
    hideMenu();
    push(`/app/plans/${planRoutes.current}`);
  }

  const { t } = props;

  return (
    <React.Fragment>
      <div className="dropdown-menu-header">
        <div className="dropdown-menu-header-inner bg-info">
          <div
            className="menu-header-image opacity-2"
            style={{
              backgroundImage: 'url(' + city + ')'
            }}
          />
          <div className="menu-header-content text-left">
            <div className="widget-content p-0">
              <div className="widget-content-wrapper">
                <div className="widget-content-left mr-3">
                  <div className="user-profile">
                    <FontAwesomeIcon
                      className="user-profile-icon"
                      icon={faUser}
                    />
                  </div>
                </div>
                <div className="widget-content-left">
                  <div className="widget-heading">
                    {props.userFirstName + ' ' + props.userLastName}{' '}
                  </div>
                </div>
                <div className="widget-content-right ml-auto mr-2">
                  <Button
                    className="btn-pill btn-shadow btn-shine"
                    color="focus"
                    onClick={onLogout}
                  >
                    {t('userSettings.signOut')}
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* <div className="scroll-area-xs"> */}
      <div>
        <PerfectScrollbar>
          <Nav vertical>
            <NavItem>
              <NavLink
                tag={Button}
                type="button"
                color="link"
                className="font-size-md w-100"
                onClick={gotToActivePlan}
              >
                {t('plans.activePlanDetails')}
              </NavLink>
            </NavItem>
            <NavItem>
              <NavLink
                tag={Button}
                type="button"
                color="link"
                className="font-size-md w-100"
                onClick={showUserSettings}
              >
                {t('userSettings.changePassword')}
              </NavLink>
            </NavItem>
            <NavItem>
              <NavLink
                className="font-size-md w-100"
                href="https://www.socialhose.io/en/user-guide"
                rel="noopener noreferrer"
                target="_blank"
              >
                {t('userSettings.userGuide')}
              </NavLink>
            </NavItem>
            <NavItem style={{ textAlign: 'start' }}>
              <div className="mt-2 mb-3 mx-3 px-1">
                <p className="text-muted font-size-md mb-2">{t('userSettings.guidedTourTooltip')}</p>
                <div className="d-flex flex-row flex-wrap pl-3">
                  {tourPages.map((tour) => (
                    <Button
                      key={tour.name}
                      className="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 mr-2"
                      outline
                      color="primary"
                      onClick={() => tourGuide(tour.to)}
                    >
                      <i className={`${tour.icon} btn-icon-wrapper mb-2`} />
                      {t(`userSettings.${tour.translateKey}`)}
                    </Button>
                  ))}
                </div>
              </div>
            </NavItem>
          </Nav>
        </PerfectScrollbar>
      </div>
    </React.Fragment>
  );
}

UserSettingsMenu.propTypes = {
  toggleMenu: PropTypes.func.isRequired,
  userFirstName: PropTypes.string.isRequired,
  userLastName: PropTypes.string.isRequired,
  actions: PropTypes.object.isRequired,
  t: PropTypes.func.isRequired
};

const applyDecorators = compose(
  reduxActions(),
  translate(['common'], { wait: true })
);

export default React.memo(applyDecorators(UserSettingsMenu));
