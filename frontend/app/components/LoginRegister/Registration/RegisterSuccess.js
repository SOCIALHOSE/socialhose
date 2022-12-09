import React, { Fragment, useEffect } from 'react';
import PropTypes from 'prop-types';
import { Col, Row } from 'reactstrap';
import logo from '../../../images/logo/logo-small.png';
import { useHistory, useLocation } from 'react-router';
import { setDocumentData } from '../../../common/helper';
import { Trans, translate } from 'react-i18next';
import LangSettingsMenu from '../../App/AppHeader/LangSettingsMenu';

function RegisterSuccess({ t }) {
  const { state } = useLocation();
  const history = useHistory();

  const email = state ? state.email : '';

  useEffect(() => {
    if (!email) {
      history.push('/auth/login');
      return;
    }

    setDocumentData('title', 'Registration Success');

    return () => setDocumentData('title'); // default
  }, []);

  if (!email) {
    return null;
  }

  return (
    <Row>
      <Col sm={12}>
        <div className="form-wizard-content bg-white shadow">
          <div className="no-results">
            <div className="text-center my-5">
              <img src={logo} style={{ height: '50px' }} />
            </div>
            <div className="sa-icon sa-success animate mb-5">
              <span className="sa-line sa-tip animateSuccessTip" />
              <span className="sa-line sa-long animateSuccessLong" />
              <div className="sa-placeholder" />
              <div className="sa-fix" />
            </div>
            <div className="results-title mb-2">
              {state && state.isFreeUser ? (
                <Fragment>
                  <Trans i18nKey="register.freeRegisterSuccess">
                    You have successfully <br className="d-block d-sm-none" />
                    <strong>Free Basic Account</strong>.
                  </Trans>
                </Fragment>
              ) : (
                <Fragment>
                  <Trans i18nKey="register.paidRegisterSuccess">
                    You have successfully paid and
                    <br className="d-block d-sm-none" />
                    registered to SOCIALHOSE.IO.
                  </Trans>
                </Fragment>
              )}
            </div>
            <p className="text-center text-muted mb-3">
              {t('register.successBottomText', { email })}
            </p>
          </div>
        </div>
      </Col>
      <div className="header-dots public-lang">
        <LangSettingsMenu direction="left" />
      </div>
    </Row>
  );
}

RegisterSuccess.propTypes = {
  t: PropTypes.func.isRequired
};

export default translate(['loginApp'], { wait: true })(RegisterSuccess);
