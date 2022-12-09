import React from 'react';
import PropTypes from 'prop-types';

import { Col, FormGroup, Label, Row } from 'reactstrap';
import { getData } from 'country-list';
import { CardElement } from '@stripe/react-stripe-js';

import { Input } from '../../../common/FormControls';
import { Trans, translate } from 'react-i18next';

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

function BillingDetailsForm(props) {
  const { form, errors, handleChange, handleValidation, t } = props;

  return (
    <Row>
      <Col md={12}>
        <Input
          name="name"
          title={t('plans.billingForm.fullName')}
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
          title={t('plans.billingForm.addr1')}
          type="text"
          required
          description={t('plans.billingForm.addr1Desc')}
          value={form.line1}
          error={errors.line1}
          handleChange={handleChange}
          handleValidation={handleValidation}
        />
      </Col>
      <Col md={6}>
        <Input
          name="line2"
          title={t('plans.billingForm.addr2')}
          type="text"
          description={t('plans.billingForm.addr2Desc')}
          value={form.line2}
          error={errors.line2}
          handleChange={handleChange}
          handleValidation={handleValidation}
        />
      </Col>
      <Col md={6}>
        <Input
          name="city"
          title={t('plans.billingForm.city')}
          type="text"
          required
          description={t('plans.billingForm.cityDesc')}
          value={form.city}
          error={errors.city}
          handleChange={handleChange}
          handleValidation={handleValidation}
        />
      </Col>
      <Col md={6}>
        <Input
          name="state"
          title={t('plans.billingForm.state')}
          type="text"
          required
          description={t('plans.billingForm.stateDesc')}
          value={form.state}
          error={errors.state}
          handleChange={handleChange}
          handleValidation={handleValidation}
        />
      </Col>
      <Col md={6}>
        <Input
          name="postal_code"
          title={t('plans.billingForm.zip')}
          type="text"
          required
          description={t('plans.billingForm.zipDesc')}
          value={form.postal_code}
          error={errors.postal_code}
          handleChange={handleChange}
          handleValidation={handleValidation}
        />
      </Col>
      <Col md={6}>
        <Input
          name="country"
          title={t('plans.billingForm.country')}
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
          title={t('plans.billingForm.email')}
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
          title={t('plans.billingForm.phone')}
          type="tel"
          required
          description={t('plans.billingForm.phoneDesc')}
          value={form.phone}
          error={errors.phone}
          handleChange={handleChange}
          handleValidation={handleValidation}
        />
      </Col>
      <Col xs={12} className="mb-2">
        <FormGroup>
          <Label>{t('plans.billingForm.cardHeading')}</Label>
          <CardElement
            options={cardElementOptions}
            className="border b-radius-5 p-3"
          />
        </FormGroup>
      </Col>
      <Col md={12}>
        <p className="text-muted">
          <Trans i18nKey="plans.billingForm.agreement">
            By submitting, you agree to our
            <a
              title="Privacy Policy"
              target="_blank"
              href="https://www.socialhose.io/en/legal/privacy"
              className="footer__link"
            >
              Privacy Policy
            </a>
            <a
              title="Terms and Conditions"
              target="_blank"
              href="https://www.socialhose.io/en/legal/terms"
              className="footer__link"
            >
              Terms & Conditions
            </a>
            <a
              title="Acceptable Use Policy"
              target="_blank"
              href="https://www.socialhose.io/en/legal/acceptable-use"
              className="footer__link"
            >
              Acceptable Use Policy
            </a>
            .
          </Trans>
        </p>
      </Col>
    </Row>
  );
}

BillingDetailsForm.propTypes = {
  t: PropTypes.func,
  form: PropTypes.object,
  errors: PropTypes.object,
  handleChange: PropTypes.func,
  handleValidation: PropTypes.func
};

export default React.memo(
  translate(['tabsContent'], { wait: true })(BillingDetailsForm)
);
