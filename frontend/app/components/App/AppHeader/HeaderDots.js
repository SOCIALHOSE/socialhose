/* eslint-disable no-unused-vars */
import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Link } from 'react-router-dom';
import {
  UncontrolledDropdown,
  DropdownToggle,
  DropdownMenu,
  Col,
  Row,
  Button,
  DropdownItem
} from 'reactstrap';

import { IoIosGrid } from 'react-icons/io';
import Notifications from './Notifications';
import LangSettingsMenu from './LangSettingsMenu';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faAngleDown } from '@fortawesome/free-solid-svg-icons';
import { planRoutes } from '../Account/Plans/UserPlans';
import { convertUTCtoLocal } from '../../../common/helper';

class HeaderDots extends React.Component {
  static propTypes = {
    mainTabs: PropTypes.array.isRequired,
    restrictions: PropTypes.object.isRequired,
    planDetails: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  validateTab(tab) {
    if (tab === 'analyze') {
      if (!this.props.restrictions) {
        return false;
      }
      const permissions = this.props.restrictions.permissions;
      return permissions.analytics;
    }
    return true;
  }

  render() {
    const { t, mainTabs, planDetails, restrictions } = this.props;
    const isFreeAccount = planDetails.price === 0;
    const isRTL = document.documentElement.dir === 'rtl';

    return (
      <div className="header-dots">
        <UncontrolledDropdown nav inNavbar>
          <DropdownToggle nav>
            {t('plans.currentPlan')}
            <FontAwesomeIcon className="ml-2 opacity-5" icon={faAngleDown} />
          </DropdownToggle>
          <DropdownMenu
            className={`dropdown-menu-rounded rm-pointers${
              isRTL ? ' dropdown-menu-left' : ''
            }`}
          >
            <div className="dropdown-menu-header">
              <div className="dropdown-menu-header-inner bg-success">
                <div className="menu-header-image opacity-1"></div>
                <div className="menu-header-content text-left">
                  <h5 className="menu-header-title font-weight-bold">
                    {isFreeAccount
                      ? t('plans.freeBasicAccount')
                      : `$${planDetails.price}`}
                  </h5>
                  {!isFreeAccount && (
                    <p>
                      {restrictions.subStartDate && restrictions.subEndDate
                        ? `${convertUTCtoLocal(
                            isRTL
                              ? restrictions.subEndDate
                              : restrictions.subStartDate,
                            'MMM D, YYYY'
                          )} - ${convertUTCtoLocal(
                            isRTL
                              ? restrictions.subStartDate
                              : restrictions.subEndDate,
                            'MMM D, YYYY'
                          )}`
                        : t('plans.perMonth')}
                    </p>
                  )}
                </div>
              </div>
            </div>
            <DropdownItem
              tag={Link}
              to={`/app/plans/${planRoutes.update}`}
              className="font-weight-bold"
            >
              <i className="dropdown-icon lnr-rocket opacity-8"> </i>
              {t('plans.upgradePlan')}
            </DropdownItem>
            <DropdownItem tag={Link} to={`/app/plans/${planRoutes.txn}`}>
              <i className="dropdown-icon lnr-list"> </i>
              {t('plans.yourTransactions')}
            </DropdownItem>
            {!isFreeAccount && (
              <DropdownItem
                tag={Link}
                to={`/app/plans/${planRoutes.changeCard}`}
              >
                <i className="dropdown-icon lnr-license"> </i>
                {t('plans.changeCard')}
              </DropdownItem>
            )}
          </DropdownMenu>
        </UncontrolledDropdown>
        <Button
          tag={Link}
          to={`/app/plans/${planRoutes.update}`}
          size="sm"
          outline
          color="success"
          className="align-self-center mr-3 d-none d-lg-block"
        >
          {t('plans.upgradePlan')}
        </Button>
        <UncontrolledDropdown className="d-block d-lg-none">
          <DropdownToggle className="p-0 mr-2" color="link">
            <div className="icon-wrapper icon-wrapper-alt rounded-circle">
              <div className="icon-wrapper-bg bg-primary" />
              <IoIosGrid color="#3f6ad8" fontSize="23px" />
            </div>
          </DropdownToggle>
          <DropdownMenu
            className={`rm-pointers${isRTL ? ' dropdown-menu-left' : ''}`}
          >
            <div className="grid-menu grid-menu-xl grid-menu-3col">
              {mainTabs.map((tab, i) => {
                if (!this.validateTab(tab)) return null;
                return (
                  <Col md="12" key={`main-tab-link-${i}`}>
                    <Button
                      className="btn-icon-vertical btn-square btn-transition"
                      outline
                      color="link"
                    >
                      <Link to={'/app/' + tab} className="nav-link">
                        <Row>
                          <i
                            className={
                              i
                                ? 'lnr lnr-exit-up  btn-icon-wrapper mr-1'
                                : 'lnr-magnifier btn-icon-wrapper mr-1'
                            }
                          ></i>
                          <p>{t('tabs.' + tab)}</p>
                        </Row>
                      </Link>
                    </Button>
                  </Col>
                );
              })}
            </div>
          </DropdownMenu>
        </UncontrolledDropdown>
        <LangSettingsMenu />
        <Notifications />
      </div>
    );
  }
}

export default translate(['common'], { wait: true })(HeaderDots);
