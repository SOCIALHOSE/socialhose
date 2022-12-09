import React, { useEffect, useRef, useState } from 'react';
import PropTypes from 'prop-types';
import { Link, useHistory, useLocation, useParams } from 'react-router-dom';
import { Button, Col, Row, Form } from 'reactstrap';
import PerfectScrollbar from 'react-perfect-scrollbar';

import CommonSection from '../CommonSection';
import useForm from '../../common/hooks/useForm';
import useIsMounted from '../../common/hooks/useIsMounted';
import logo from '../../../images/logo/logo-small.png';
import { reduxActions } from '../../../redux/utils/connect';
import { employeesOptions } from './BasicDetailsPage';
import {
  registerUser,
  submitHubspot
} from '../../../api/registration/registration';
import { validateForm } from '../../../common/helper';
import { Input } from '../../common/FormControls';
import { registerSteps } from './Register';
import { industryList } from './PlanConstants';
import { Trans, translate } from 'react-i18next';
import LangSettingsMenu from '../../App/AppHeader/LangSettingsMenu';

const initialBasicForm = {
  email: '',
  firstName: '',
  lastName: '',
  companyName: '',
  jobFunction: '',
  numberOfEmployee: '',
  industry: '',
  websiteUrl: '',
  password: '',
  confirmPassword: '',
  errors: {
    email: null,
    firstName: null,
    lastName: null,
    companyName: null,
    jobFunction: null,
    numberOfEmployee: null,
    industry: null,
    password: null,
    confirmPassword: null
  }
};

function RegisterFree({ actions, t }) {
  const { search } = useLocation();
  const { push, replace } = useHistory();
  const { step } = useParams();
  const isMounted = useIsMounted();
  const [loading, setLoading] = useState(false);

  const {
    form,
    errors,
    handleChange,
    handleValidation,
    validateSubmit,
    resetForm
  } = useForm(initialBasicForm);

  const [disabledFields, setDisabledFields] = useState([]);
  const ref = useRef();

  const searchParams = new URLSearchParams(search);

  useEffect(() => {
    if (step !== registerSteps[0]) {
      replace(`/auth/register/${registerSteps[0]}`);
    }

    const obj = {};
    const objErrs = {};
    for (let [key, value] of searchParams) {
      if (Object.keys(initialBasicForm).includes(key) && value) {
        obj[key] = value;
        objErrs[key] = false;
      }
    }
    setDisabledFields(Object.keys(obj));
    resetForm({
      ...form,
      ...obj,
      errors: {
        ...errors,
        ...objErrs
      }
    });
  }, []);

  const handleSubmit = async () => {
    const basicForm = validateSubmit();
    if (!basicForm) {
      actions.addAlert({
        type: 'error',
        transKey: 'requiredInfo'
      });
      return;
    }

    const obj = validateForm(
      { password: form.password, confirmPassword: form.confirmPassword },
      { password: errors.password, confirmPassword: errors.confirmPassword },
      handleValidation
    );
    if (!obj) {
      return actions.addAlert({
        type: 'error',
        transKey: 'requiredInfo'
      });
    }

    setLoading(true);
    delete basicForm.confirmPassword;
    registerUser(basicForm).then((res) => {
      if (!isMounted.current) {
        return;
      }

      if (res.error) {
        res.data
          ? actions.addAlert(res.data)
          : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
        setLoading(false);
        return;
      }

      window.gtag &&
        window.gtag('event', 'sign_up', {
          method: 'Free'
        });

      submitHubspot({
        ...basicForm,
        lifecyclestage: 'marketingqualifiedlead' // label: Marketing Qualified Lead
      }).then(() => {
        push('/auth/register-success', {
          email: basicForm.email,
          isFreeUser: res.data.isFreeUser
        });
      });
    });
  };

  function onPasswordValidate(name, error) {
    if (name === 'password') {
      handleValidation('password', error);
    }
    if (form.confirmPassword !== '' && form.password !== form.confirmPassword) {
      handleValidation('confirmPassword', t('register.passwordNotMatched'));
    } else if (form.confirmPassword !== '') {
      handleValidation('confirmPassword', null);
    }
  }

  const isRTL = document.documentElement.dir === 'rtl';
  return (
    <Row className="vh-100 no-gutters registration">
      <Col
        lg="8"
        md="12"
        className="vh-100 d-flex bg-white justify-content-center position-relative"
      >
        <PerfectScrollbar
          className="w-100 position-relative"
          // eslint-disable-next-line react/jsx-no-bind
          containerRef={(container) => {
            ref.current = container;
          }}
        >
          <div className="text-center mt-5 mb-3">
            <a href="http://socialhose.io/" rel="noopener noreferrer" target="_blank">
              <img src={logo} style={{ height: '40px' }} />
            </a>
          </div>
          <Col
            lg="9"
            md="10"
            sm="12"
            className="mx-auto app-login-box mt-4 mb-5"
          >
            <Form>
              <Row>
                <Col md={12}>
                  <Input
                    name="email"
                    title={t('register.labels.email')}
                    type="email"
                    required
                    disabled={disabledFields.includes('email')}
                    value={form.email}
                    error={errors.email}
                    handleChange={
                      !disabledFields.includes('email')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="firstName"
                    title={t('register.labels.firstName')}
                    type="text"
                    required
                    disabled={disabledFields.includes('firstName')}
                    value={form.firstName}
                    error={errors.firstName}
                    handleChange={
                      !disabledFields.includes('firstName')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="lastName"
                    title={t('register.labels.lastName')}
                    type="text"
                    required
                    disabled={disabledFields.includes('lastName')}
                    value={form.lastName}
                    error={errors.lastName}
                    handleChange={
                      !disabledFields.includes('lastName')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="companyName"
                    title={t('register.labels.company')}
                    type="text"
                    required
                    disabled={disabledFields.includes('companyName')}
                    value={form.companyName}
                    error={errors.companyName}
                    handleChange={
                      !disabledFields.includes('companyName')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="jobFunction"
                    title={t('register.labels.jobFunction')}
                    type="text"
                    required
                    disabled={disabledFields.includes('jobFunction')}
                    value={form.jobFunction}
                    error={errors.jobFunction}
                    handleChange={
                      !disabledFields.includes('jobFunction')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="numberOfEmployee"
                    title={t('register.labels.employees')}
                    type="select"
                    required
                    disabled={disabledFields.includes('numberOfEmployee')}
                    options={employeesOptions}
                    value={form.numberOfEmployee}
                    error={errors.numberOfEmployee}
                    handleChange={
                      !disabledFields.includes('numberOfEmployee')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="industry"
                    title={t('register.labels.industry')}
                    type="select"
                    required
                    options={industryList}
                    disabled={disabledFields.includes('industry')}
                    value={form.industry}
                    error={errors.industry}
                    handleChange={
                      !disabledFields.includes('industry')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={12}>
                  <Input
                    name="websiteUrl"
                    title={t('register.labels.websiteURL')}
                    type="text"
                    disabled={disabledFields.includes('websiteUrl')}
                    value={form.websiteUrl}
                    error={errors.websiteUrl}
                    handleChange={
                      !disabledFields.includes('websiteUrl')
                        ? handleChange
                        : undefined
                    }
                    handleValidation={handleValidation}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="password"
                    title={t('register.labels.password')}
                    type="password"
                    placeholder={t('register.placeholders.password')}
                    required
                    value={form.password}
                    error={errors.password}
                    handleChange={handleChange}
                    handleValidation={onPasswordValidate}
                  />
                </Col>
                <Col md={6}>
                  <Input
                    name="confirmPassword"
                    title={t('register.labels.confirmPassword')}
                    type="password"
                    placeholder={t('register.placeholders.confirmPassword')}
                    required
                    value={form.confirmPassword}
                    error={errors.confirmPassword}
                    handleChange={handleChange}
                    handleValidation={onPasswordValidate}
                  />
                </Col>
                <Col md={12}>
                  <p className="text-muted">
                    <Trans i18nKey="register.agreement">
                      By registering, you agree to our
                      <a
                        title="Privacy Policy"
                        target="_blank"
                        href="https://www.socialhose.io/en/legal/privacy"
                      >
                        Privacy Policy
                      </a>
                      <a
                        title="Terms and Conditions"
                        target="_blank"
                        href="https://www.socialhose.io/en/legal/terms"
                      >
                        Terms & Conditions
                      </a>
                      <a
                        title="Acceptable Use Policy"
                        target="_blank"
                        href="https://www.socialhose.io/en/legal/acceptable-use"
                      >
                        Acceptable Use Policy
                      </a>
                      .
                    </Trans>
                  </p>
                </Col>
              </Row>
              <Row className="divider" />
              <div className="d-flex flex-column-reverse flex-sm-row justify-content-sm-between">
                <h6 className="mt-3">
                  {t('register.signInText')}{' '}
                  <Link className="text-primary" to="/auth/login">
                    {t('register.signInBtn')}
                  </Link>
                </h6>
                <Button
                  type="button"
                  color="primary"
                  disabled={loading}
                  onClick={handleSubmit}
                  className="btn-wide btn-hover-shine mb-2 mb-sm-0"
                  size="lg"
                >
                  {loading ? t('register.loading') : t('register.registerBtn')}
                </Button>
              </div>
            </Form>
          </Col>
          <div className="header-dots public-lang">
            <LangSettingsMenu direction={isRTL ? 'right' : 'left'} />
          </div>
        </PerfectScrollbar>
      </Col>
      <CommonSection />
    </Row>
  );
}

RegisterFree.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object.isRequired
};

export default reduxActions()(
  translate(['loginApp'], { wait: true })(RegisterFree)
);
