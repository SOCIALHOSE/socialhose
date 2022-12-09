import React, { Fragment, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { CardElement, useElements, useStripe } from '@stripe/react-stripe-js';
import { Alert, Button, Card, CardBody, CardTitle, Col, Row } from 'reactstrap';

import { reduxActions } from '../../../../redux/utils/connect';
import useForm from '../../../common/hooks/useForm';
import useIsMounted from '../../../common/hooks/useIsMounted';
import BillingDetailsForm from './BillingDetailsForm';
import { changeCardDetails } from '../../../../api/plans/userPlans';
import { planRoutes } from './UserPlans';
import { setDocumentData } from '../../../../common/helper';
import { translate } from 'react-i18next';

const initialForm = {
  name: '',
  line1: '',
  line2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  email: '',
  phone: '',
  errors: {
    name: null,
    line1: null,
    city: null,
    state: null,
    postal_code: null,
    country: null,
    email: null,
    phone: null
  }
};

function ChangeCard({ actions, t }) {
  const isMounted = useIsMounted();
  const stripe = useStripe();
  const elements = useElements();

  const {
    form,
    errors,
    handleChange,
    handleValidation,
    validateSubmit
  } = useForm(initialForm);
  const [paymentError, setPaymentError] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    setDocumentData('title', 'Change Card');

    return () => setDocumentData('title'); // default
  }, []);

  const submitPayment = async () => {
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

    const newObj = {};
    newObj.paymentID = paymentMethod.id; //stripe card element ID
    const res = await changeCardDetails(newObj);

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

    actions.addAlert({ type: 'notice', transKey: 'cardUpdated' });
    // refresh page on success and move to active plan details
    setTimeout(() => {
      window.location.pathname = `/app/plans/${planRoutes.current}`;
    }, 1000);
  };

  return (
    <Col xs="12" lg="8" xl="9">
      <Card className="mb-3">
        <CardBody>
          <CardTitle>{t('plans.changeCard.heading')}</CardTitle>
          <p className="text-muted mb-3">{t('plans.changeCard.subText')}</p>
          <BillingDetailsForm
            form={form}
            errors={errors}
            handleChange={handleChange}
            handleValidation={handleValidation}
          />
          <Row className="divider" />
          {paymentError && (
            <Alert color="danger">
              <Fragment>
                <p className="font-size-xs font-weight-bold text-uppercase">
                  {t('plans.changeCard.error')}
                </p>
                {paymentError.message}
              </Fragment>
            </Alert>
          )}
          <div className="text-right">
            <Button
              type="button"
              color="primary"
              onClick={submitPayment}
              disabled={!stripe || !elements || loading}
              className="btn-wide btn-hover-shine mb-2 mb-sm-0"
              size="lg"
            >
              {loading
                ? t('plans.changeCard.loadingBtn')
                : t('plans.changeCard.changeCardBtn')}
            </Button>
          </div>
        </CardBody>
      </Card>
    </Col>
  );
}

ChangeCard.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object
};

export default reduxActions()(
  translate(['tabsContent'], { wait: true })(ChangeCard)
);
