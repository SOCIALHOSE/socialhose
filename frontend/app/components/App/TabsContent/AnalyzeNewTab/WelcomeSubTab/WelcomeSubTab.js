import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Link } from 'react-router-dom'
import { compose } from 'redux'
import { Card, Col, Row } from 'reactstrap'

class WelcomeSubTab extends React.Component {
  render () {
    const { t } = this.props

    return (
      <Card className="py-md-5 mb-3">
        <Row className="justify-content-center no-gutters">
          <Col sm="6" md="4" xl="4" className="m-4">
            <div className="border b-radius-5 text-center p-4">
              <div className="icon-wrapper mb-4 rounded-circle">
                <div className="icon-wrapper-bg bg-primary" />
                <i className="lnr-plus-circle text-primary" />
              </div>
              <div>
                <h5 className="mb-5">{t('analyzeTab.createNewAnalysis')}</h5>
                <Link
                  to="/app/analyze/create"
                  className="btn btn-primary btn-block fsize-1 btn-lg mr-1"
                >
                  {t('analyzeTab.go')}
                </Link>
              </div>
            </div>
          </Col>
          <Col sm="6" md="4" xl="4" className="m-4">
            <div className="border b-radius-5 text-center p-4">
              <div className="icon-wrapper mb-4 rounded-circle">
                <div className="icon-wrapper-bg bg-primary" />
                <i className="lnr-list text-primary" />
              </div>
              <div>
                <h5 className="mb-5">{t('analyzeTab.viewSavedAnalysis')}</h5>
                <Link
                  to="/app/analyze/saved"
                  className="btn btn-primary btn-block fsize-1 btn-lg mr-1"
                >
                  {t('analyzeTab.view')}
                </Link>
              </div>
            </div>
          </Col>
        </Row>
      </Card>
    )
  }
}

WelcomeSubTab.propTypes = {
  t: PropTypes.func.isRequired
}

const applyDecorators = compose(translate(['tabsContent'], { wait: true }))

export default applyDecorators(WelcomeSubTab)
