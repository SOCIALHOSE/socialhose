/* eslint-disable react/jsx-no-bind */
import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Link, useHistory } from 'react-router-dom';
import { Col, Row, Button } from 'reactstrap';
import { Input } from '../../common/FormControls';

import { registerSteps } from './Register';
import { reduxActions } from '../../../redux/utils/connect';
import {
  registerUser,
  submitHubspot
} from '../../../api/registration/registration';
import { setDocumentData, validateForm } from '../../../common/helper';

function FreeAccountPage({
  form = {},
  errors,
  handleChange,
  handleValidation,
  validateSubmitBasic,
  completedSteps,
  actions
}) {
  const { replace, push } = useHistory();
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!completedSteps[registerSteps[0]]) {
      replace(`/auth/register/${registerSteps[0]}`);
      return;
    }

    setDocumentData('title', 'Register');

    return () => setDocumentData('title'); // default
  }, []);

  const handleSubmit = async () => {
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

    const basicForm = validateSubmitBasic();
    if (!basicForm) {
      actions.addAlert({
        type: 'error',
        transKey: 'requiredInfo'
      });
      push(`/auth/register/${registerSteps[0]}`);
      return;
    }

    setLoading(true);
    basicForm.password = obj.password;
    registerUser(basicForm).then((res) => {
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
      handleValidation('confirmPassword', 'Confirm Password does not match.');
    } else if (form.confirmPassword !== '') {
      handleValidation('confirmPassword', null);
    }
  }

  return (
    <Col lg="9" md="10" sm="12" className="mx-auto app-login-box mt-4 mb-5">
      <h4>
        <span>Create password for your free basic account</span>
      </h4>
      <Row>
        <Col md={6}>
          <Input
            name="password"
            title="Password"
            type="password"
            placeholder="Enter Password"
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
            title="Confirm Password"
            type="password"
            placeholder="Confirm Password"
            required
            value={form.confirmPassword}
            error={errors.confirmPassword}
            handleChange={handleChange}
            handleValidation={onPasswordValidate}
          />
        </Col>
        <Col md={12}>
          <p className="text-muted">
            By registering, you agree to our{' '}
            <a
              title="Privacy Policy"
              target="_blank"
              href="https://www.socialhose.io/en/legal/privacy"
            >
              Privacy Policy
            </a>
            ,{' '}
            <a
              title="Terms and Conditions"
              target="_blank"
              href="https://www.socialhose.io/en/legal/terms"
            >
              Terms & Conditions
            </a>{' '}
            and{' '}
            <a
              title="Acceptable Use Policy"
              target="_blank"
              href="https://www.socialhose.io/en/legal/acceptable-use"
            >
              Acceptable Use Policy
            </a>
            .
          </p>
        </Col>
      </Row>
      <Row className="divider" />
      <div className="d-flex flex-column-reverse flex-sm-row justify-content-sm-between">
        <Link
          to={`/auth/register/${registerSteps[1]}`}
          className={loading ? 'pointer-events-none' : ''}
        >
          <Button type="button" color="secondary" size="lg" disabled={loading}>
            Back
          </Button>
        </Link>
        <Button
          type="button"
          color="primary"
          disabled={loading}
          onClick={handleSubmit}
          className="btn-wide btn-hover-shine mb-2 mb-sm-0"
          size="lg"
        >
          {loading ? 'Loading...' : 'Register'}
        </Button>
      </div>
    </Col>
  );
}

FreeAccountPage.propTypes = {
  form: PropTypes.object,
  errors: PropTypes.object,
  handleChange: PropTypes.func,
  handleValidation: PropTypes.func,
  validateSubmitBasic: PropTypes.func,
  completedSteps: PropTypes.object,
  actions: PropTypes.object
};

export default React.memo(reduxActions()(FreeAccountPage));
