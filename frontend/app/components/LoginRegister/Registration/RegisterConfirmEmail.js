import React, { Fragment, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import { Button, Col, Row } from 'reactstrap';
import { useHistory, useParams } from 'react-router';
import { activeAccount } from '../../../api/registration/registration';
import Loading from '../../common/Loading';
import logo from '../../../images/logo/logo-small.png';
import { setDocumentData } from '../../../common/helper';
import { translate } from 'react-i18next';
import LangSettingsMenu from '../../App/AppHeader/LangSettingsMenu';

function RegisterConfirmEmail({ t }) {
  const { token } = useParams();
  const history = useHistory();
  const [msg, setMsg] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!token) {
      history.push('/auth/login');
      return;
    }

    setLoading(true);
    activeAccount(token).then((res) => {
      if (res.error) {
        setMsg({
          text: t('register.verification.failed')
        });
        setLoading(false);
        return;
      }

      window.gtag &&
        window.gtag('event', 'email_verified', {
          email_verified: true
        });

      setMsg({
        isSuccess: true,
        text: t('register.verification.success')
      });
      setLoading(false);
    });

    setDocumentData('title', 'Account Verification');

    return () => setDocumentData('title'); // default
  }, []);

  if (!token) {
    return null;
  }

  return (
    <Row>
      <Col sm={12}>
        <div className="form-wizard-content bg-white shadow">
          <div className="no-results">
            <div className="text-center mb-5">
              <img src={logo} style={{ height: '50px' }} />
            </div>
            {loading && <Loading />}
            {!loading && (
              <Fragment>
                {msg.isSuccess ? (
                  <Fragment>
                    <div className="sa-icon animate sa-success">
                      <span className="sa-line sa-tip animateSuccessTip" />
                      <span className="sa-line sa-long animateSuccessLong" />
                      <div className="sa-placeholder" />
                      <div className="sa-fix" />
                    </div>
                    <div className="results-title mb-3">{msg.text}</div>
                    <Button
                      size="lg"
                      color="primary"
                      tag={Link}
                      to="/auth/login"
                      className="btn-wide btn-pill mb-5"
                    >
                      {t('register.verification.loginBtn')}
                    </Button>
                  </Fragment>
                ) : (
                  <Fragment>
                    <div className="sa-icon sa-error animateErrorIcon">
                      <span className="sa-x-mark animateXMark">
                        <span className="sa-line sa-left"></span>
                        <span className="sa-line sa-right"></span>
                      </span>
                    </div>
                    <div className="results-title mb-5">{msg.text}</div>
                  </Fragment>
                )}
              </Fragment>
            )}
          </div>
        </div>
      </Col>
      <div className="header-dots public-lang">
        <LangSettingsMenu direction="left" />
      </div>
    </Row>
  );
}

RegisterConfirmEmail.propTypes = {
  t: PropTypes.func.isRequired
};

export default translate(['loginApp'], { wait: true })(RegisterConfirmEmail);
