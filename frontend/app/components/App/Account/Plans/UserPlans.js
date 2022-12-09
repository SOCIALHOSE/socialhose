import React, { useEffect } from 'react';
import PropTypes from 'prop-types';
import {
  NavLink,
  Redirect,
  Route,
  Switch,
  useRouteMatch
} from 'react-router-dom';
import reduxConnect from '../../../../redux/utils/connect';
import ChangeCard from './ChangeCard';
import CurrentPlan from './CurrentPlan';
import UpdatePlan from './UpdatePlan';
import UserTransactions from './UserTransactions';
import { Card, CardBody, Col, Row } from 'reactstrap';
import { translate } from 'react-i18next';

export const planRoutes = {
  current: 'current',
  changeCard: 'change-card',
  txn: 'transactions',
  update: 'update'
};

function UserPlans({ actions, restrictions, t }) {
  const match = useRouteMatch();

  useEffect(() => {
    const { setEnableClosedSidebar } = actions;
    actions.getRestrictions();
    setEnableClosedSidebar(true);

    return () => setEnableClosedSidebar(false);
  }, []);

  return (
    <Row>
      <Col xs={12} lg={4} xl={3}>
        <Card className="mb-3">
          <CardBody className="navigation-vertical">
            <ul className="navigation-ul">
              <li className="navigation-item">
                <NavLink
                  className="navigation-link"
                  activeClassName="active"
                  to={`${match.url}/${planRoutes.current}`}
                >
                  <em>
                    <i className="font-size-lg lnr-file-empty"> </i>
                  </em>
                  <span>{t('plans.sidebar.activePlanDetails')}</span>
                </NavLink>
              </li>
              {restrictions.isPaymentId && (
                <li>
                  <NavLink
                    className="navigation-link"
                    activeClassName="active"
                    to={`${match.url}/${planRoutes.changeCard}`}
                  >
                    <em>
                      <i className="font-size-lg lnr-license"> </i>
                    </em>
                    <span>{t('plans.sidebar.changeCard')}</span>
                  </NavLink>
                </li>
              )}
              <li>
                <NavLink
                  className="navigation-link"
                  activeClassName="active"
                  to={`${match.url}/${planRoutes.update}`}
                >
                  <em>
                    <i className="font-size-lg lnr-arrow-up-circle"> </i>
                  </em>
                  <span>{t('plans.sidebar.updatePlan')}</span>
                </NavLink>
              </li>
              <li>
                <NavLink
                  className="navigation-link"
                  activeClassName="active"
                  to={`${match.url}/${planRoutes.txn}`}
                >
                  <em>
                    <i className="font-size-lg lnr-list"> </i>
                  </em>
                  <span>{t('plans.sidebar.yourTransactions')}</span>
                </NavLink>
              </li>
            </ul>
          </CardBody>
        </Card>
      </Col>
      <Switch>
        <Route path={`${match.url}/${planRoutes.current}`}>
          <CurrentPlan />
        </Route>
        {restrictions.isPaymentId && (
          <Route path={`${match.url}/${planRoutes.changeCard}`}>
            <ChangeCard />
          </Route>
        )}
        <Route path={`${match.url}/${planRoutes.txn}`}>
          <UserTransactions />
        </Route>
        <Route path={`${match.url}/${planRoutes.update}`}>
          <UpdatePlan />
        </Route>
        <Redirect to={`${match.url}/current`} />
      </Switch>
    </Row>
  );
}

UserPlans.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object.isRequired,
  restrictions: PropTypes.object.isRequired
};

export default reduxConnect('restrictions', [
  'common',
  'auth',
  'user',
  'restrictions'
])(translate(['tabsContent'], { wait: true })(UserPlans));
