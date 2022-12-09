import React, { Fragment, useEffect, useState } from 'react';
import { useHistory } from 'react-router';
import PropTypes from 'prop-types';
import { Button, Card, CardBody, CardTitle, Col, Row } from 'reactstrap';
import reduxConnect from '../../../../redux/utils/connect';
import { planRoutes } from './UserPlans';
import { allMediaTypes } from '../../../../redux/modules/appState/searchByFilters';
import { capitalize } from 'lodash';
import { convertUTCtoLocal, setDocumentData } from '../../../../common/helper';
import { translate } from 'react-i18next';
import CancellationFeedback from './CancellationFeedback';

function CurrentPlan({ actions, user, t }) {
  const [cancelModal, setCancelModal] = useState(false);

  const { restrictions } = user;
  const { push } = useHistory();

  useEffect(() => {
    setDocumentData('title', 'Active Plan Details');

    return () => setDocumentData('title'); // default
  }, []);

  function changePlan() {
    push(`/app/plans/${planRoutes.update}`);
  }

  function toggleCancelModal() {
    setCancelModal((prev) => !prev);
  }

  const {
    plans,
    limits,
    isPlanCancelled,
    subStartDate,
    subEndDate
  } = restrictions;

  const selectedMedias = [];
  const notSelectedMedias = [];

  allMediaTypes.map((v) => {
    if (plans[v]) {
      selectedMedias.push(t(`searchTab.sourceTypes.${v}`, capitalize(v)));
    } else {
      notSelectedMedias.push(t(`searchTab.sourceTypes.${v}`, capitalize(v)));
    }
  });

  const isRTL = document.documentElement.dir === 'rtl';
  return (
    <Col xs="12" lg="8" xl="9">
      <Row>
        <Col sm="6" md="4">
          <div className="card mb-3 widget-chart text-left">
            <div className="widget-chart-content">
              <div className="widget-subheading">
                {t('plans.currentPlan.subHeading')}
              </div>
              <div className="widget-numbers">
                {plans.price === 0
                  ? t('plans.currentPlan.freePlan')
                  : `$${plans.price}`}
              </div>
              <div className="widget-description">
                <span>
                  {plans.price === 0 ? (
                    <Fragment>&nbsp;</Fragment>
                  ) : subStartDate && subEndDate ? (
                    `${convertUTCtoLocal(
                      subStartDate,
                      'MMM D, YYYY'
                    )} - ${convertUTCtoLocal(subEndDate, 'MMM D, YYYY')}`
                  ) : (
                    t('plans.currentPlan.perMonth')
                  )}
                </span>
              </div>
            </div>
          </div>
        </Col>
        <Col sm="6">
          <button
            className="card mb-3 widget-chart bg-success text-white text-left"
            onClick={changePlan}
          >
            <div className="widget-chart-content">
              <div className="widget-subheading">
                {t('plans.currentPlan.changePlan')}
              </div>
              <div className="widget-numbers font-size-xlg">
                {t('plans.currentPlan.upgradeYourPlan')}
              </div>
              <div className="widget-description">
                <span>{t('plans.currentPlan.upgradeText')}</span>
              </div>
            </div>
          </button>
        </Col>
        <Col xs="12">
          <Card>
            <CardBody>
              <CardTitle>{t('plans.currentPlan.currentPlanDetails')}</CardTitle>
              <div className="mb-3">
                <p className="text-muted">
                  {t('plans.currentPlan.selectedMediaTypes')}
                </p>
                <p className="font-size-xlg">
                  {selectedMedias.length > 0
                    ? selectedMedias.join(', ')
                    : t('plans.currentPlan.none')}
                  {notSelectedMedias.length > 0 ? (
                    <span className="font-size-md opacity-6 ml-2">
                      ({t('plans.currentPlan.upgradeToGet')}:{' '}
                      {notSelectedMedias.join(', ')})
                    </span>
                  ) : (
                    ''
                  )}
                </p>
              </div>
              <div className="divider" />
              <div className="mb-3">
                <p className="text-muted mb-2">
                  {t('plans.currentPlan.selectedLicenses')}
                </p>
                <Row>
                  <Col xs="12" sm="6" md="3">
                    <div className="mb-3 card widget-chart">
                      {!isRTL ? (
                        <div className="widget-numbers">
                          {limits.savedFeeds.current}/{limits.savedFeeds.limit}
                        </div>
                      ) : (
                        <div className="widget-numbers">
                          {limits.savedFeeds.limit}/{limits.savedFeeds.current}
                        </div>
                      )}
                      <div className="widget-subheading mb-3">
                        {t('plans.currentPlan.feedsLicenses')}
                      </div>
                    </div>
                  </Col>
                  <Col xs="12" sm="6" md="3">
                    <div className="mb-3 card widget-chart">
                      {!isRTL ? (
                        <div className="widget-numbers">
                          {limits.searchesPerDay.current}/
                          {limits.searchesPerDay.limit}
                        </div>
                      ) : (
                        <div className="widget-numbers">
                          {limits.searchesPerDay.limit}/
                          {limits.searchesPerDay.current}
                        </div>
                      )}
                      <div className="widget-subheading mb-3">
                        {t('plans.currentPlan.searchLicenses')}
                      </div>
                    </div>
                  </Col>
                  <Col xs="12" sm="6" md="3">
                    <div className="mb-3 card widget-chart">
                      {!isRTL ? (
                        <div className="widget-numbers">
                          {limits.webFeeds.current}/{limits.webFeeds.limit}
                        </div>
                      ) : (
                        <div className="widget-numbers">
                          {limits.webFeeds.limit}/{limits.webFeeds.current}
                        </div>
                      )}
                      <div className="widget-subheading mb-3">
                        {t('plans.currentPlan.webfeedLicenses')}
                      </div>
                    </div>
                  </Col>
                  <Col xs="12" sm="6" md="3">
                    <div className="mb-3 card widget-chart">
                      {!isRTL ? (
                        <div className="widget-numbers">
                          {limits.alerts.current}/{limits.alerts.limit}
                        </div>
                      ) : (
                        <div className="widget-numbers">
                          {limits.alerts.limit}/{limits.alerts.current}
                        </div>
                      )}
                      <div className="widget-subheading mb-3">
                        {t('plans.currentPlan.alertLicenses')}
                      </div>
                    </div>
                  </Col>
                  <Col xs="12" sm="6" md="3">
                    <div className="mb-3 card widget-chart">
                      {!isRTL ? (
                        <div className="widget-numbers">
                          {limits.subscriberAccounts.current}/
                          {limits.subscriberAccounts.limit}
                        </div>
                      ) : (
                        <div className="widget-numbers">
                          {limits.subscriberAccounts.limit}/
                          {limits.subscriberAccounts.current}
                        </div>
                      )}
                      <div className="widget-subheading mb-3">
                        {t('plans.currentPlan.userAccounts')}
                      </div>
                    </div>
                  </Col>
                </Row>
              </div>
              <div className="divider" />
              <div className="mb-3">
                <p className="text-muted">{t('plans.currentPlan.features')}</p>
                <p className="font-size-xlg">
                  {plans.analytics ? (
                    t('plans.currentPlan.analytics')
                  ) : (
                    <Fragment>
                      {t('plans.currentPlan.none')}
                      <span className="font-size-md opacity-6 ml-2">
                        ({t('plans.currentPlan.upgradeToGet')}:{' '}
                        {t('plans.currentPlan.analytics')})
                      </span>
                    </Fragment>
                  )}
                </p>
              </div>
              {plans.price > 0 && (
                <Fragment>
                  <div className="divider" />
                  <div className="mb-3">
                    {!isPlanCancelled ? (
                      <div className="text-muted">
                        <Button
                          color="danger"
                          outline
                          onClick={toggleCancelModal}
                        >
                          {t('plans.currentPlan.cancelSubscriptionBtn')}
                        </Button>
                        <p className="text-muted mt-2">
                          {t('plans.currentPlan.cancelWarning')}
                        </p>
                      </div>
                    ) : (
                      <div className="text-muted">
                        <Button color="secondary" outline disabled>
                          {t('plans.currentPlan.cancelSubscriptionBtn')}
                        </Button>
                        <p className="d-block d-md-inline-block ml-md-3 mt-md-0 mt-2 ml-0 text-muted">
                          {t('plans.currentPlan.alreadyCancelled')}
                        </p>
                      </div>
                    )}
                  </div>
                </Fragment>
              )}
              <CancellationFeedback
                isOpen={cancelModal}
                toggle={toggleCancelModal}
                actions={actions}
                user={user}
                t={t}
              />
            </CardBody>
          </Card>
        </Col>
      </Row>
    </Col>
  );
}

CurrentPlan.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object,
  user: PropTypes.object
};

export default reduxConnect('user', ['common', 'auth', 'user'])(
  translate(['tabsContent'], { wait: true })(CurrentPlan)
);
