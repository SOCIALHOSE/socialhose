import React, { Fragment, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import city3 from '../../../styles/utils/images/dropdown-header/city3.jpg';
import PerfectScrollbar from 'react-perfect-scrollbar';
import reduxConnect from '../../../redux/utils/connect';
import cl from 'classnames';
import {
  Alert,
  Button,
  DropdownMenu,
  DropdownToggle,
  Nav,
  NavItem,
  UncontrolledDropdown
} from 'reactstrap';
import { Interpolate, translate } from 'react-i18next';
import { compose } from 'redux';
import { IoIosNotificationsOutline } from 'react-icons/io';

function Notifications({ alerts, t, actions }) {
  const [alertsList, setAlertsList] = useState([]);

  useEffect(() => {
    // Empty list when mounts
    actions.removeAllAlerts();
  }, []);

  useEffect(() => {
    const newAlerts = alerts
      .reverse()
      .map((alert) => {
        return typeof alert === 'string' ? { message: alert } : alert;
      })
      .map((alert) => {
        const interpolateParameters = alert ? alert.parameters : {};
        const i18nKey = alert && `alerts.${alert.type}.${alert.transKey}`;
        let type, msg;

        type = alert.type ? oldValueMapping[alert.type] : 'warning';

        msg = t(i18nKey, {
          ...interpolateParameters,
          defaultValue: alert.message || t('error.unknown')
        });

        return { type, msg };
      });

    setAlertsList(newAlerts);
  }, [alerts.length]);

  const isRTL = document.documentElement.dir === 'rtl';

  return (
    <UncontrolledDropdown>
      <DropdownToggle className="p-0 mr-2" color="link">
        <div className="icon-wrapper icon-wrapper-alt rounded-circle">
          <div className="icon-wrapper-bg bg-danger" />
          <IoIosNotificationsOutline color="#d92550" fontSize="23px" />
          <div className="badge badge-dot badge-dot-sm badge-danger">
            {alertsList.length > 0 ? t('userSettings.notifications') : ''}
          </div>
        </div>
      </DropdownToggle>
      <DropdownMenu
        className={cl('dropdown-menu-xl rm-pointers', {
          'py-0': alertsList.length < 1,
          'dropdown-menu-left': isRTL
        })}
      >
        <div className="dropdown-menu-header mb-0">
          <div className="dropdown-menu-header-inner bg-deep-blue">
            <div
              className="menu-header-image opacity-1"
              style={{
                backgroundImage: 'url(' + city3 + ')'
              }}
            />
            <div className="menu-header-content text-dark">
              <h5 className="menu-header-title">
                {t('userSettings.notifications')}
              </h5>
              <h6 className="menu-header-subtitle">
                <Interpolate
                  i18nKey={
                    alertsList.length > 1
                      ? 'userSettings.notificationsSub_plural'
                      : 'userSettings.notificationsSub'
                  }
                  alertLength={alertsList.length}
                />
              </h6>
            </div>
          </div>
        </div>
        {alertsList.length > 0 && (
          <Fragment>
            <div className="scroll-area-md">
              <PerfectScrollbar>
                <div className="p-2">
                  {alertsList.map((item, i) => (
                    <Alert
                      key={i}
                      className="mb-2"
                      style={{ wordBreak: 'break-word' }}
                      color={colorsMapping[item.type]}
                    >
                      <p className="font-size-xs font-weight-bold text-uppercase">
                        {item.type}
                      </p>
                      {item.msg}
                    </Alert>
                  ))}
                </div>
              </PerfectScrollbar>
            </div>
            <Nav vertical>
              <NavItem className="nav-item-divider" />
              <NavItem className="nav-item-btn text-center">
                <Button
                  size="sm"
                  className="btn-shadow btn-wide btn-pill"
                  color="focus"
                  onClick={actions.removeAllAlerts}
                >
                  {t('userSettings.clearAll')}
                </Button>
              </NavItem>
            </Nav>
          </Fragment>
        )}
      </DropdownMenu>
    </UncontrolledDropdown>
  );
}

const oldValueMapping = {
  notice: 'success',
  warning: 'warning',
  error: 'error'
};

const colorsMapping = {
  success: 'success',
  warning: 'warning',
  error: 'danger'
};

Notifications.propTypes = {
  t: PropTypes.func.isRequired,
  alerts: PropTypes.array.isRequired,
  actions: PropTypes.object.isRequired
};

const applyDecorators = compose(
  reduxConnect('alerts', ['common', 'alerts']),
  translate(['common'], { wait: true })
);

export default applyDecorators(Notifications);
