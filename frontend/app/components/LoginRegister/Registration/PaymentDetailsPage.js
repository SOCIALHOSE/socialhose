/* eslint-disable react/jsx-no-bind */
import React, { useState, Fragment, useEffect } from 'react';
import PropTypes from 'prop-types';
import { Link, useHistory } from 'react-router-dom';
import { CardElement, useStripe, useElements } from '@stripe/react-stripe-js';
import { Col, Row, Button, Alert, FormGroup, Label } from 'reactstrap';
import { getData } from 'country-list';

import { reduxActions } from '../../../redux/utils/connect';
import {
  registerUser,
  submitHubspot
} from '../../../api/registration/registration';
import { Input } from '../../common/FormControls';
import { registerSteps } from './Register';

const countries = getData().map((v) => ({ label: v.name, value: v.code }));

const cardElementOptions = {
  hidePostalCode: true,
  style: {
    base: {
      fontSize: '16px',
      color: '#424770'
    },
    invalid: {
      color: '#d92550'
    }
  }
};

function PaymentDetailsPage({
  form = {},
  errors,
  handleChange,
  handleValidation,
  updatingPrice,
  validateSubmitBasic,
  totalCost,
  planDetails,
  completedSteps,
  validateSubmit,
  actions
}) {
  const stripe = useStripe();
  const elements = useElements();
  const { push, replace } = useHistory();
  const [paymentError, setPaymentError] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!completedSteps[registerSteps[0]]) {
      replace(`/auth/register/${registerSteps[0]}`);
      return;
    }
    if (!completedSteps[registerSteps[1]]) {
      replace(`/auth/register/${registerSteps[1]}`);
      return;
    }
  }, []);

  const handleSubmit = async () => {
    if (!stripe || !elements) {
      // Stripe.js has not loaded yet.
      return;
    }

    setPaymentError(false);
    setLoading(true);

    const obj = validateSubmit();
    if (!obj) {
      setLoading(false);
      return actions.addAlert({
        type: 'error',
        transKey: 'requiredInfo'
      });
    }

    const cardElement = elements.getElement(CardElement);
    const {
      name,
      line1,
      line2,
      city,
      state,
      postal_code,
      country,
      email,
      phone
    } = obj;
    const { error, paymentMethod } = await stripe.createPaymentMethod({
      type: 'card',
      card: cardElement,
      billing_details: {
        name,
        email,
        phone,
        address: {
          line1: line1,
          line2: line2,
          city: city,
          state: state,
          postal_code: postal_code,
          country: country
        }
      }
    });

    if (error) {
      setPaymentError(error);
      setLoading(false);
      return;
    }

    const basicForm = validateSubmitBasic();
    if (!basicForm) {
      setLoading(false);
      actions.addAlert({
        type: 'error',
        transKey: 'requiredInfo'
      });
      return push(`/auth/register/${registerSteps[0]}`);
    }

    const newObj = { ...planDetails, ...basicForm };
    newObj.password = obj.password;
    newObj.masterAccounts = '1';
    newObj.paymentID = paymentMethod.id; //stripe card element ID
    const res = await registerUser(newObj);

    if (res.error) {
      res.data
        ? actions.addAlert(res.data)
        : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
      setLoading(false);
      return;
    }

    if (res.data && res.data.paymentError) {
      setPaymentError({ message: res.data.message });
      setLoading(false);
      return;
    }

    window.gtag && window.gtag('event', 'sign_up', {
      method: 'Paid',
      amount_paid: totalCost
    });

    window.gtag && window.gtag('event', 'purchase', {
      currency: 'USD',
      value: totalCost
    });

    submitHubspot({
      ...basicForm,
      lifecyclestage: 'customer' // label: Customer
    }).then(() => {
      push('/auth/register-success', {
        email: basicForm.email,
        isFreeUser: res.data.isFreeUser
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
      <div className="mb-4">
        <h4>
          <span>Create Password</span>
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
        </Row>
        <Row className="divider" />
      </div>
      <div className="mb-4">
        <h4>
          <span>Billing and Payment Details</span>
        </h4>
        <Row>
          <Col md={12}>
            <Input
              name="name"
              title="Full Name"
              type="text"
              required
              value={form.name}
              error={errors.name}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="line1"
              title="Address Line 1"
              type="text"
              required
              description="e.g., street, PO Box, or company name"
              value={form.line1}
              error={errors.line1}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="line2"
              title="Address Line 2"
              type="text"
              description="e.g., apartment, suite, unit, or building"
              value={form.line2}
              error={errors.line2}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="city"
              title="City"
              type="text"
              required
              description="City, district, suburb, town, or village"
              value={form.city}
              error={errors.city}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="state"
              title="State"
              type="text"
              required
              description="State, county, province, or region"
              value={form.state}
              error={errors.state}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="postal_code"
              title="Zip"
              type="text"
              required
              description="ZIP or postal code"
              value={form.postal_code}
              error={errors.postal_code}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="country"
              title="Country"
              type="select"
              required
              options={countries}
              value={form.country}
              error={errors.country}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="email"
              title="Billing Email"
              type="email"
              required
              value={form.email}
              error={errors.email}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="phone"
              title="Billing Phone Number"
              type="tel"
              required
              description="Phone number including extension"
              value={form.phone}
              error={errors.phone}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          </Col>
          <Col xs={12} className="mb-2">
            <FormGroup>
              <Label>Card Details</Label>
              <CardElement
                options={cardElementOptions}
                className="border b-radius-5 p-3"
              />
            </FormGroup>
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
        {paymentError && (
          <Alert color="danger">
            <Fragment>
              <p className="font-size-xs font-weight-bold text-uppercase">
                Error
              </p>
              {paymentError.message}
            </Fragment>
          </Alert>
        )}
        {paymentError && (
          <p className="mb-3">
            You can also choose your plan later. Click to get your
            <Link
              to={`/auth/register/${registerSteps[3]}`}
              className="font-weight-bold ml-1"
            >
              Free Basic Account
            </Link>
            .
          </p>
        )}
        <div className="d-flex justify-content-between flex-column-reverse flex-sm-row">
          <Link
            to={`/auth/register/${registerSteps[1]}`}
            className={loading ? 'pointer-events-none' : ''}
          >
            <Button
              type="button"
              color="secondary"
              size="lg"
              disabled={loading}
            >
              Back
            </Button>
          </Link>
          <Button
            type="button"
            color="primary"
            onClick={handleSubmit}
            disabled={!stripe || !elements || loading}
            className="btn-wide btn-hover-shine mb-2 mb-sm-0"
            size="lg"
          >
            {loading ? 'Loading...' : `Pay $${totalCost} & Register`}
          </Button>
        </div>
      </div>
    </Col>
  );
}

PaymentDetailsPage.propTypes = {
  changePlan: PropTypes.func,
  form: PropTypes.object,
  errors: PropTypes.object,
  planDetails: PropTypes.object,
  handleChange: PropTypes.func,
  handleValidation: PropTypes.func,
  validateSubmitBasic: PropTypes.func,
  validateSubmit: PropTypes.func,
  actions: PropTypes.object,
  completedSteps: PropTypes.object,
  updatingPrice: PropTypes.bool,
  totalCost: PropTypes.oneOfType([PropTypes.number, PropTypes.string])
};

export default React.memo(reduxActions()(PaymentDetailsPage));
