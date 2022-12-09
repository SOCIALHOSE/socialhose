import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Row, Col, Progress } from 'reactstrap';

export class Restrictions extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    restrictions: PropTypes.object,
    restrictionsIds: PropTypes.array
  };

  getBarColor(percentage) {
    return percentage > 50
      ? percentage > 75
        ? 'danger'
        : 'warning'
      : 'success';
  }

  render() {
    const { restrictions, restrictionsIds, t } = this.props;
    if (!restrictions) return '';
    let searchLicense = null;
    let searchLicenseLimit = null;
    let saveLicense = null;
    let saveLicenseLimit = null;
    let alerts = null;
    let alertsLimit = null;
    let webFeeds = null;
    let webFeedsLimit = null;
    let isAlerts = false;

    restrictionsIds.map((id) => {
      const restriction = restrictions[id];
      if (id === 'alerts') {
        alerts = restriction.current;
        alertsLimit = restriction.limit;
        isAlerts = true;
      }

      if (id === 'searchesPerDay') {
        searchLicense = restriction.current;
        searchLicenseLimit = restriction.limit;
      }

      if (id === 'savedFeeds') {
        saveLicense = restriction.current;
        saveLicenseLimit = restriction.limit;
      }

      if (id === 'webFeeds') {
        webFeeds = restriction.current;
        webFeedsLimit = restriction.limit;
      }
    });

    const alertPerc = (alerts * 100) / alertsLimit;
    const searchPerc = (searchLicense * 100) / searchLicenseLimit;
    const feedPerc = (saveLicense * 100) / saveLicenseLimit;
    const webFeedPerc = (webFeeds * 100) / webFeedsLimit;

    return (
      <Fragment>
        {isAlerts ? (
          <Row>
            <Col md="6" xl="4">
              <div className="card mb-3 widget-content">
                <div className="widget-content-outer">
                  <div className="widget-content-wrapper">
                    <div className="widget-content-left">
                      <div className="widget-heading">
                        {t('restrictions.alertLicenses')}
                      </div>
                      <div className="widget-subheading">
                        {t('restrictions.perMonth')}
                      </div>
                    </div>
                    <div className="widget-content-right">
                      <div
                        className={`widget-numbers text-${this.getBarColor(
                          alertPerc
                        )}`}
                      >
                        {alertsLimit}
                      </div>
                    </div>
                  </div>
                  <div className="widget-progress-wrapper">
                    <Progress
                      className="progress-bar-sm progress-bar-animated-alt"
                      color={this.getBarColor(alertPerc)}
                      value={alertPerc}
                    />
                    <div className="progress-sub-label">
                      {alerts} / {alertsLimit}
                    </div>
                  </div>
                </div>
              </div>
            </Col>
            {/* {restrictions.newsletters && (
              <Col md="6" xl="4">
                <div className="card mb-3 widget-content">
                  <div className="widget-content-outer">
                    <div className="widget-content-wrapper">
                      <div className="widget-content-left">
                        <div className="widget-heading">{t('restrictions.totalNewsltter')}</div>
                      </div>
                      <div className="widget-content-right">
                        <div className="widget-numbers text-primary">
                          {restrictions.newsletters.limit}
                        </div>
                      </div>
                    </div>
                    <div className="widget-progress-wrapper">
                      <Progress
                        className="progress-bar-sm progress-bar-animated-alt"
                        color="warning"
                        value={20}
                      />
                      <div className="progress-sub-label">
                        {restrictions.newsletters.current} /{' '}
                        {restrictions.newsletters.limit}
                      </div>
                    </div>
                  </div>
                </div>
              </Col>
            )} */}
            <Col md="6" xl="4">
              <div className="card mb-3 widget-content">
                <div className="widget-content-outer">
                  <div className="widget-content-wrapper">
                    <div className="widget-content-left">
                      <div className="widget-heading">
                        {t('restrictions.webfeedLicenses')}
                      </div>
                      <div className="widget-subheading">
                        {t('restrictions.perMonth')}
                      </div>
                    </div>
                    <div className="widget-content-right">
                      <div
                        className={`widget-numbers text-${this.getBarColor(
                          webFeedPerc
                        )}`}
                      >
                        {webFeedsLimit}
                      </div>
                    </div>
                  </div>
                  <div className="widget-progress-wrapper">
                    <Progress
                      className="progress-bar-sm progress-bar-animated-alt"
                      color={this.getBarColor(webFeedPerc)}
                      value={webFeedPerc}
                    />
                    <div className="progress-sub-label">
                      {webFeeds} / {webFeedsLimit}
                    </div>
                  </div>
                </div>
              </div>
            </Col>
          </Row>
        ) : (
          <Row data-tour="search-licenses">
            <Col md="6" xl="4">
              <div className="card mb-3 widget-content">
                <div className="widget-content-outer">
                  <div className="widget-content-wrapper">
                    <div className="widget-content-left">
                      <div className="widget-heading">
                        {t('restrictions.searchLicenses')}
                      </div>
                      <div className="widget-subheading">
                        {t('restrictions.perDay')}
                      </div>
                    </div>
                    <div className="widget-content-right">
                      <div
                        className={`widget-numbers text-${this.getBarColor(
                          searchPerc
                        )}`}
                      >
                        {searchLicenseLimit}
                      </div>
                    </div>
                  </div>
                  <div className="widget-progress-wrapper">
                    <Progress
                      className="progress-bar-sm progress-bar-animated-alt"
                      color={this.getBarColor(searchPerc)}
                      value={searchPerc}
                    />
                    <div className="progress-sub-label">
                      {searchLicense} / {searchLicenseLimit}
                    </div>
                  </div>
                </div>
              </div>
            </Col>
            <Col md="6" xl="4">
              <div className="card mb-3 widget-content">
                <div className="widget-content-outer">
                  <div className="widget-content-wrapper">
                    <div className="widget-content-left">
                      <div className="widget-heading">
                        {t('restrictions.feedLicenses')}
                      </div>
                      <div className="widget-subheading">
                        {t('restrictions.perMonth')}
                      </div>
                    </div>
                    <div className="widget-content-right">
                      <div
                        className={`widget-numbers text-${this.getBarColor(
                          feedPerc
                        )}`}
                      >
                        {saveLicenseLimit}
                      </div>
                    </div>
                  </div>
                  <div className="widget-progress-wrapper">
                    <Progress
                      className="progress-bar-sm progress-bar-animated-alt"
                      color={this.getBarColor(feedPerc)}
                      value={feedPerc}
                    />
                    <div className="progress-sub-label">
                      {saveLicense} / {saveLicenseLimit}
                    </div>
                  </div>
                </div>
              </div>
            </Col>
          </Row>
        )}
      </Fragment>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(Restrictions);
