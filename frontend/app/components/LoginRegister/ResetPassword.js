import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { compose } from 'redux';
import { Link } from 'react-router-dom';
import { Button, Col, Form, FormGroup, Input, Label, Row } from 'reactstrap';
import reduxConnect from '../../redux/utils/connect';
import CommonSection from './CommonSection';
import { setDocumentData } from '../../common/helper';
import LangSettingsMenu from '../App/AppHeader/LangSettingsMenu';

function ResetPassword(props) {
  const { history, t } = props;
  const [password, setPassword] = useState('');

  useEffect(() => {
    const confirmationToken = location.search.split('=')[1];
    if (!confirmationToken) {
      history.push('/auth/forgot-password');
      return;
    }

    setDocumentData('title', 'Reset Password');

    return () => setDocumentData('title'); // default
  }, []);

  function changeHandler({ target: { value } }) {
    setPassword(value);
  }

  function submitHandler(e) {
    e.preventDefault();
    const confirmationToken = location.search.split('=')[1];
    if (password && confirmationToken) {
      props.actions.confirmPasswordReset(confirmationToken, password);
    }
  }

  return (
    <div className="h-100">
      <Row className="h-100 no-gutters">
        <CommonSection />
        <Col
          lg="8"
          md="12"
          className="h-100 d-flex bg-white justify-content-center align-items-center position-relative"
        >
          <Col lg="6" md="8" sm="12" className="mx-auto app-login-box">
            <div className="app-logo" />
            <h4>
              <div>{t('resetPass.mainLabel')}</div>
              <span>{t('resetPass.subLabel')}</span>
            </h4>
            <div>
              <Form onSubmit={submitHandler}>
                <Row form>
                  <Col md={12}>
                    <FormGroup>
                      <Label for="password">
                        {t('resetPass.newPasswordLabel')}
                      </Label>
                      <Input
                        id="password"
                        name="password"
                        type="password"
                        value={password}
                        placeholder={t('resetPass.newPasswordPlaceholder')}
                        onChange={changeHandler}
                      />
                    </FormGroup>
                  </Col>
                </Row>
                <div className="mt-4 d-flex align-items-center">
                  <h6 className="mb-0">
                    <Link to="/auth/login" className="text-primary">
                      {t('resetPass.signIn')}
                    </Link>
                  </h6>
                  <div className="ml-auto">
                    <Button color="primary" size="lg" type="submit">
                      {t('resetPass.resetBtn')}
                    </Button>
                  </div>
                </div>
              </Form>
            </div>
          </Col>
          <div className="header-dots public-lang">
            <LangSettingsMenu direction="left" />
          </div>
        </Col>
      </Row>
    </div>
  );
}

ResetPassword.propTypes = {
  store: PropTypes.object.isRequired,
  actions: PropTypes.object.isRequired,
  history: PropTypes.object.isRequired,
  t: PropTypes.func.isRequired
};

const applyDecorators = compose(
  reduxConnect(),
  translate(['loginApp', 'common'], { wait: true })
);

export default applyDecorators(ResetPassword);
