import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { compose } from 'redux';
import { Link } from 'react-router-dom';
import {
  Alert,
  Button,
  Col,
  Form,
  FormGroup,
  Input,
  Label,
  Row
} from 'reactstrap';
import reduxConnect from '../../redux/utils/connect';
import CommonSection from './CommonSection';
import { setDocumentData } from '../../common/helper';
import LangSettingsMenu from '../App/AppHeader/LangSettingsMenu';

function ForgotPassword({ register, actions, t }) {
  const [email, setEmail] = useState('');

  useEffect(() => {
    setDocumentData('title', 'Forgot Password');

    return () => setDocumentData('title'); // default
  }, []);

  function changeHandler({ target: { value } }) {
    setEmail(value);
  }

  function submitHandler(e) {
    e.preventDefault();
    actions.requestPasswordReset(email);
  }

  const successMessage = register && register.successMessage;
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
              <div>{t('forgotPass.mainLabel')}</div>
              <span>{t('forgotPass.subLabel')}</span>
            </h4>
            <div>
              {successMessage && (
                <Alert color="success" isOpen toggle={actions.clearMessages}>
                  {successMessage}
                </Alert>
              )}
              {!successMessage && (
                <Form onSubmit={submitHandler}>
                  <Row form>
                    <Col md={12}>
                      <FormGroup>
                        <Label for="email-address">
                          {t('forgotPass.emailLabel')}
                        </Label>
                        <Input
                          id="email-address"
                          name="email"
                          type="email"
                          value={email}
                          placeholder={t('forgotPass.emailPlaceholder')}
                          onChange={changeHandler}
                        />
                      </FormGroup>
                    </Col>
                  </Row>
                  <div className="mt-4 d-flex align-items-center">
                    <h6 className="mb-0">
                      <Link to="/auth/login" className="text-primary">
                        {t('forgotPass.signIn')}
                      </Link>
                    </h6>
                    <div className="ml-auto">
                      <Button color="primary" size="lg" type="submit">
                        {t('forgotPass.resetBtn')}
                      </Button>
                    </div>
                  </div>
                </Form>
              )}
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

ForgotPassword.propTypes = {
  register: PropTypes.object.isRequired,
  actions: PropTypes.object.isRequired,
  t: PropTypes.func.isRequired
};

const applyDecorators = compose(
  reduxConnect('register', ['common', 'register']),
  translate(['loginApp', 'common'], { wait: true })
);

export default applyDecorators(ForgotPassword);
