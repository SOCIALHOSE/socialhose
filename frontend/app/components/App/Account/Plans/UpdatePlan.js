/* eslint-disable react/jsx-no-bind */
import React, { Fragment, useCallback, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import Slider from 'rc-slider';
import Tooltip from 'rc-tooltip';
import {
  Alert,
  Button,
  Card,
  CardBody,
  CardTitle,
  Col,
  Form,
  FormGroup,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row
} from 'reactstrap';

import {
  licenses,
  mediaTypes,
  features,
  addonFeatures
} from '../../../LoginRegister/Registration/PlanConstants';
import useForm from '../../../common/hooks/useForm';
import { debounce } from 'lodash';
import { CardElement, useStripe, useElements } from '@stripe/react-stripe-js';

import useIsMounted from '../../../common/hooks/useIsMounted';
import reduxConnect from '../../../../redux/utils/connect';
import {
  getPlans,
  updatePrice
} from '../../../../api/registration/registration';
import {
  updatePlanHubspot,
  updatePlanPayment
} from '../../../../api/plans/userPlans';
import { planRoutes } from './UserPlans';
import BillingDetailsForm from './BillingDetailsForm';

import simpleNumberLocalizer from 'react-widgets-simple-number';
import NumberPicker from 'react-widgets/lib/NumberPicker';
import LoadersAdvanced from '../../../common/Loader/Loader';
import { IoIosWarning } from 'react-icons/io';
import { convertUTCtoLocal, setDocumentData } from '../../../../common/helper';
import { translate } from 'react-i18next';

simpleNumberLocalizer();

const Handle = Slider.Handle;

const handle = (props) => {
  // eslint-disable-next-line react/prop-types
  const { value, dragging, index, ...restProps } = props;

  return (
    <Tooltip
      key={index}
      prefixCls="rc-slider-tooltip"
      overlay={value}
      visible={dragging}
      placement="top"
    >
      <Handle value={value} {...restProps} />
    </Tooltip>
  );
};

const initialForm = {
  savedFeeds: 0,
  searchesPerDay: 0,
  webFeeds: 0,
  alerts: 0,
  news: 0,
  blog: 0,
  reddit: 0,
  instagram: 0,
  twitter: 0,
  analytics: 0,
  subscriberAccounts: 0,
  masterAccounts: 0
};

const initialPaymentForm = {
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

function UpdatePlan({ actions, restrictions, t }) {
  const stripe = useStripe();
  const elements = useElements();
  const isMounted = useIsMounted();

  // first step
  const { form, handleChange, resetForm } = useForm(initialForm);
  const [updatingPrice, setUpdatingPrice] = useState(true);
  const [totalCost, setTotalCost] = useState(' - ');
  const [modal, setModal] = useState(false);
  const [loading, setLoading] = useState(false);
  const [planLoading, setPlanLoading] = useState(true);
  const [planError, setPlanError] = useState(false);
  const [planList, setPlanList] = useState([]);
  const [disableUpdate, setDisableUpdate] = useState(true);

  // second step
  const [nextStep, setNextStep] = useState(false);
  const {
    form: paymentForm,
    handleChange: handlePaymentForm,
    errors: paymentFormErrors,
    handleValidation: handlePaymentValidation,
    validateSubmit
  } = useForm(initialPaymentForm);
  const [paymentError, setPaymentError] = useState(false);
  const [paymentLoading, setPaymentLoading] = useState(false);

  // to update price when input changes
  useEffect(() => {
    if (planList.length > 0) {
      debouncePrice(form);
    }
  }, [...Object.values(form)]);

  const debouncePrice = useCallback(
    debounce((form) => {
      setUpdatingPrice(true);
      updatePrice(form).then((res) => {
        if (!isMounted.current) {
          return false;
        }
        if (res.error || isNaN(res.data.totalPrice)) {
          actions.addAlert(res.data);
          setUpdatingPrice(false);
          setTotalCost('Error');
          return;
        }
        setTotalCost(res.data.totalPrice);
        setUpdatingPrice(false);
      });
    }, 1000),
    [isMounted.current]
  );

  useEffect(() => {
    if (!restrictions.isPlanCancelled && !restrictions.isPlanDowngrade) {
      setDisableUpdate(false);
    } else {
      setDisableUpdate(true);
    }
  }, [restrictions.isPlanCancelled, restrictions.isPlanDowngrade]);

  useEffect(() => {
    getBillingPlans();

    setDocumentData('title', 'Update Plan');
    return () => setDocumentData('title'); // default
  }, []);

  function getBillingPlans() {
    setPlanLoading(true);
    setPlanError(false);
    getPlans().then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data || !res.data.length) {
        setPlanError(true);
        setPlanLoading(false);
        res.data && res.data.length > 0 && actions.addAlert(res.data);
        return;
      }
      setPlanLoading(false);
      setPlanList(res.data);

      const modified = { ...initialForm };
      let selectedPlan = {};
      if (restrictions.plans.price > 0) {
        selectedPlan = { ...restrictions.plans };
        Object.entries(restrictions.limits).map(([key, value]) => {
          selectedPlan[key] = value.limit;
        });
        selectedPlan.blog = selectedPlan.blogs;
        delete selectedPlan.blogs;
      } else {
        selectedPlan = res.data[0];
      }

      Object.keys(initialForm).map((key) => {
        modified[key] =
          selectedPlan[key] === undefined
            ? modified[key]
            : selectedPlan[key] === true
            ? 1
            : selectedPlan[key] === false
            ? 0
            : selectedPlan[key];
      });
      resetForm(modified);
    });
  }

  function changePlan(id) {
    const selectedPlan = planList.find((plan) => plan.id === id);
    const modified = { ...initialForm };
    Object.keys(initialForm).map((key) => {
      modified[key] =
        selectedPlan[key] === undefined
          ? modified[key]
          : selectedPlan[key] === true
          ? 1
          : selectedPlan[key] === false
          ? 0
          : selectedPlan[key];
    });
    resetForm(modified);
  }

  function handleSubmit() {
    if (restrictions.isPlanCancelled || restrictions.isPlanDowngrade) {
      return;
    }
    // move to payment page if new basic user
    // instruct according to upgrade and downgrade
    // if card already stored then only update the plan by showing modal or providing option to change card
    setLoading(true);
    if (restrictions.isPaymentId) {
      setModal(true); // show details of card
    } else {
      setNextStep(true);
      window.scrollTo(0, 0);
    }

    setLoading(false);
  }

  function toggle() {
    setModal((prev) => !prev);
  }

  function proceedToDetails() {
    toggle();
    setNextStep(true);
    window.scrollTo(0, 0);
  }

  const submitPayment = async () => {
    if (!stripe || !elements) {
      // Stripe.js has not loaded yet.
      return;
    }

    if (restrictions.isPlanCancelled || restrictions.isPlanDowngrade) {
      return;
    }

    setPaymentError(false);
    setPaymentLoading(true);

    const obj = validateSubmit();
    if (!obj) {
      setPaymentLoading(false);
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
      setPaymentLoading(false);
      return;
    }

    const newObj = { ...form };
    newObj.masterAccounts = '1';
    newObj.paymentID = paymentMethod.id; //stripe card element ID
    const res = await updatePlanPayment(newObj);

    if (res.error) {
      res.data
        ? actions.addAlert(res.data)
        : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
      setPaymentLoading(false);
      return;
    }

    window.gtag &&
      window.gtag('event', 'purchase', {
        currency: 'USD',
        value: totalCost
      });

    await updatePlanHubspot({ ...obj, ...form, totalCost });

    actions.addAlert({ type: 'notice', transKey: 'planUpdated' });

    // refresh page on success and move to active plan details
    setTimeout(() => {
      window.location.pathname = `/app/plans/${planRoutes.current}`;
    }, 1000);
  };

  const proceedPayment = async () => {
    // payment with old card
    setLoading(true);

    const newObj = { ...form };
    newObj.masterAccounts = '1';
    const res = await updatePlanPayment(newObj);

    if (res.error) {
      res.data
        ? actions.addAlert(res.data)
        : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
      setLoading(false);
      return;
    }

    window.gtag &&
      window.gtag('event', 'purchase', {
        currency: 'USD',
        value: totalCost
      });

    await updatePlanHubspot({ ...form, totalCost });

    actions.addAlert({ type: 'notice', transKey: 'planUpdated' });

    // refresh page on success and move to active plan details
    setTimeout(() => {
      window.location.pathname = `/app/plans/${planRoutes.current}`;
    }, 1000);
  };

  function moveBack() {
    window.scrollTo(0, 0);
    setNextStep(false);
  }

  if (planError || planLoading) {
    return (
      <Col xs="12" lg="8" xl="9">
        <Card className="h-75 mb-3">
          <CardBody>
            <CardTitle>{t('plans.updatePlan.heading')}</CardTitle>
            {planError && (
              <div className="text-danger text-center p-4">
                <IoIosWarning
                  className="d-block mx-auto mb-2"
                  fontSize="32px"
                />
                {t('plans.updatePlan.planLoadingFailed')}{' '}
                <Button color="link" onClick={getBillingPlans} className="p-0">
                  {t('plans.updatePlan.tryAgainBtn')}
                </Button>
              </div>
            )}
          </CardBody>
          {planLoading && <LoadersAdvanced />}
        </Card>
      </Col>
    );
  }

  const isRTL = document.documentElement.dir === 'rtl';

  return (
    <Col xs="12" lg="8" xl="9">
      <Card className="mb-3">
        {!nextStep ? (
          <CardBody>
            <CardTitle>{t('plans.updatePlan.heading')}</CardTitle>
            <p className="text-muted">
              {t('plans.updatePlan.subText')}{' '}
              <a
                href="https://www.socialhose.io/en/pricing"
                rel="noopener noreferrer"
                target="_blank"
              >
                {t('plans.updatePlan.learnMoreBtn')}
              </a>
              .
            </p>
            <hr />
            <Form>
              <Row>
                <Col md={12}>
                  <div className="mb-3">
                    <h6 className="font-weight-bold mb-3">
                      {t('plans.updatePlan.prePlans')}
                    </h6>
                    <div className="d-flex flex-wrap justify-content-center justify-content-md-start">
                      {planList.map((plan) => (
                        <Button
                          outline
                          key={plan.id}
                          color="primary"
                          type="button"
                          className="btn-wide btn-lg p-sm-3 mb-2 mr-2"
                          onClick={() => changePlan(plan.id)}
                        >
                          {plan.name}
                        </Button>
                      ))}
                    </div>
                  </div>
                  <hr />
                  <div className="mb-3">
                    <h6 className="font-weight-bold mb-3">
                      {t('plans.updatePlan.mediaTypes')}
                    </h6>
                    <div>
                      {mediaTypes.map((type) => (
                        <Button
                          key={type.name}
                          size="lg"
                          type="button"
                          title={
                            form[type.name]
                              ? 'Click to deselect'
                              : 'Click to select'
                          }
                          outline={!form[type.name]}
                          className="btn-pill mb-2 mr-2"
                          color={form[type.name] ? 'success' : 'light'}
                          onClick={() =>
                            handleChange(type.name, !form[type.name])
                          }
                        >
                          {t(`searchTab.sourceTypes.${type.transKey}`)} (
                          {type.price})
                        </Button>
                      ))}
                    </div>
                  </div>
                  <hr />
                  <div className="mb-3">
                    <h6 className="font-weight-bold mb-3">
                      {t('plans.updatePlan.licenses')}
                    </h6>
                    <Row noGutters className="justify-content-center">
                      {licenses.map((license) => (
                        <Col sm={6} key={license.name}>
                          <div className="p-4 m-2 border b-radius-5 shadow-sm">
                            <FormGroup>
                              <div className="d-flex justify-content-between">
                                <Label title={license.title}>
                                  {t(`plans.currentPlan.${license.transKey}`)}
                                </Label>
                                <span className="font-size-lg font-weight-bold text-primary">
                                  {form[license.name]}
                                </span>
                              </div>
                              <Slider
                                {...license.props}
                                reverse={isRTL}
                                handle={handle}
                                value={form[license.name]}
                                onChange={(val) =>
                                  handleChange(license.name, val)
                                }
                              />
                            </FormGroup>
                          </div>
                        </Col>
                      ))}
                    </Row>
                  </div>
                  <hr />
                  <Row>
                    <Col md="6">
                      <div className="mb-3">
                        <h6 className="font-weight-bold mb-3">
                          {t('plans.updatePlan.features')}
                        </h6>
                        <div>
                          {features.map((type) => (
                            <Button
                              key={type.name}
                              size="lg"
                              type="button"
                              title={
                                form[type.name]
                                  ? t('plans.updatePlan.deselectTooltip')
                                  : t('plans.updatePlan.selectTooltip')
                              }
                              outline={!form[type.name]}
                              className="btn-pill mb-2 mr-2"
                              color={form[type.name] ? 'success' : 'light'}
                              onClick={() =>
                                handleChange(type.name, !form[type.name])
                              }
                            >
                              {t(`plans.currentPlan.${type.transKey}`)} (
                              {type.price})
                            </Button>
                          ))}
                          <div className="pl-2">
                            {features.map((type) =>
                              form[type.name] ? (
                                <p
                                  key={type.name}
                                  className="font-size-sm text-muted mb-1"
                                >
                                  {type.desc}
                                </p>
                              ) : null
                            )}
                          </div>
                        </div>
                      </div>
                    </Col>
                    <Col md="6">
                      <div className="mb-3">
                        <h6 className="font-weight-bold mb-3">
                          {t('plans.updatePlan.addOns')}
                        </h6>
                        <Row className="px-3">
                          {addonFeatures.map((type) => (
                            <Col xs="12" key={type.name}>
                              <FormGroup>
                                <Label>
                                  {t(`plans.currentPlan.${type.transKey}`)}
                                </Label>
                                <NumberPicker
                                  {...type.props}
                                  value={form[type.name]}
                                  onChange={(val) =>
                                    handleChange(type.name, val)
                                  }
                                />
                              </FormGroup>
                            </Col>
                          ))}
                        </Row>
                      </div>
                    </Col>
                  </Row>
                  <div className="widget-content total-price">
                    <div className="widget-content-wrapper justify-content-start justify-content-md-end mr-5">
                      <div className="widget-content-left">
                        <div className="widget-heading">
                          {t('plans.updatePlan.totalCost')}
                        </div>
                        <div className="widget-subheading">
                          {t('plans.updatePlan.monthly')}
                        </div>
                      </div>
                      <div className="widget-content-right position-relative ml-0 ml-5">
                        {/* {updatingPrice && (
                          <div className="widget-numbers position-absolute text-secondary px-3">
                            <FontAwesomeIcon icon={faSpinner} pulse />
                          </div>
                        )} */}
                        <div
                          className={`widget-numbers text-warning ${
                            updatingPrice ? 'opacity-3' : ''
                          }`}
                        >
                          ${totalCost}
                        </div>
                      </div>
                    </div>
                  </div>
                </Col>
              </Row>
              <hr />
              {restrictions.isPlanCancelled || restrictions.isPlanDowngrade ? (
                <p className="text-danger mb-3">
                  {t('plans.updatePlan.cancelledWarning', {
                    text: restrictions.isPlanCancelled
                      ? 'cancelled'
                      : 'downgraded'
                  })}{' '}
                  {restrictions.subStartDate && restrictions.subEndDate
                    ? `(${convertUTCtoLocal(
                        restrictions.subStartDate,
                        'MMM D, YYYY'
                      )} - ${convertUTCtoLocal(
                        restrictions.subEndDate,
                        'MMM D, YYYY'
                      )})`
                    : ''}
                </p>
              ) : (
                ''
              )}
              <div className="text-right">
                <Button
                  type="button"
                  disabled={updatingPrice || loading || disableUpdate}
                  onClick={handleSubmit}
                  className="btn-wide"
                  color="primary"
                  size="lg"
                >
                  {loading
                    ? t('plans.updatePlan.continueBtnLoading')
                    : t('plans.updatePlan.continueBtn')}
                </Button>
              </div>
            </Form>
          </CardBody>
        ) : (
          <CardBody>
            <CardTitle>{t('plans.updatePlan.billingHeading')}</CardTitle>
            <BillingDetailsForm
              form={paymentForm}
              errors={paymentFormErrors}
              handleChange={handlePaymentForm}
              handleValidation={handlePaymentValidation}
            />
            <Row className="divider" />
            {paymentError && (
              <Alert color="danger">
                <Fragment>
                  <p className="font-size-xs font-weight-bold text-uppercase">
                    {t('plans.updatePlan.error')}
                  </p>
                  {paymentError.message}
                </Fragment>
              </Alert>
            )}
            <div className="d-flex justify-content-between flex-column-reverse flex-sm-row">
              <Button
                type="button"
                color="secondary"
                size="lg"
                disabled={paymentLoading}
                onClick={moveBack}
              >
                {t('plans.updatePlan.back')}
              </Button>
              <Button
                type="button"
                color="primary"
                onClick={submitPayment}
                disabled={!stripe || !elements || paymentLoading}
                className="btn-wide btn-hover-shine mb-2 mb-sm-0"
                size="lg"
              >
                {paymentLoading
                  ? t('plans.updatePlan.payLoading')
                  : t('plans.updatePlan.payBtn', { totalCost })}
              </Button>
            </div>
          </CardBody>
        )}
      </Card>
      <Modal isOpen={modal} toggle={toggle} backdrop="static">
        <ModalHeader toggle={toggle}>
          {t('plans.updatePlan.confirmationHeading')}
        </ModalHeader>
        <ModalBody>
          <div>
            {restrictions.plans && restrictions.plans.price > 0 ? (
              restrictions.plans.price === totalCost ? null : restrictions.plans
                  .price < totalCost ? (
                <p className="text-muted mb-3">
                  {t('plans.updatePlan.upgradeNotice')}
                </p>
              ) : (
                <p className="text-muted mb-3">
                  {t('plans.updatePlan.downgradeNotice')}
                </p>
              )
            ) : null}
            <p>{t('plans.updatePlan.alreadyStoredCard')}</p>
          </div>
        </ModalBody>
        <ModalFooter>
          <Button color="link" onClick={proceedToDetails} disabled={loading}>
            {t('plans.updatePlan.payWithOtherCardBtn')}
          </Button>
          <Button color="primary" disabled={loading} onClick={proceedPayment}>
            {loading
              ? t('plans.updatePlan.payLoading')
              : t('plans.updatePlan.payWithStoredCardBtn')}
          </Button>
        </ModalFooter>
      </Modal>
    </Col>
  );
}

UpdatePlan.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object,
  restrictions: PropTypes.object
};

export default reduxConnect('restrictions', [
  'common',
  'auth',
  'user',
  'restrictions'
])(translate(['tabsContent'], { wait: true })(UpdatePlan));
