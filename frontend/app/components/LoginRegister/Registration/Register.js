import React, { useCallback, useEffect, useRef, useState } from 'react';
import PropTypes from 'prop-types';
import { useHistory, useLocation, useParams } from 'react-router-dom';
import { Button, Col, Row } from 'reactstrap';
import { debounce } from 'lodash';
import PerfectScrollbar from 'react-perfect-scrollbar';

import { getPlans, updatePrice } from '../../../api/registration/registration';
import CommonSection from '../CommonSection';
import useForm from '../../common/hooks/useForm';
import useIsMounted from '../../common/hooks/useIsMounted';
import PlanDetailsPage from './PlanDetailsPage';
import PaymentDetailsPage from './PaymentDetailsPage';
import FreeAccountPage from './FreeAccountPage';
import BasicDetailsPage from './BasicDetailsPage';
import logo from '../../../images/logo/logo-small.png';
import Loading from '../../common/Loading';
import { reduxActions } from '../../../redux/utils/connect';
import { IoIosWarning } from 'react-icons/io';
import { setDocumentData } from '../../../common/helper';

const initialBasicForm = {
  email: '',
  firstName: '',
  lastName: '',
  companyName: '',
  jobFunction: '',
  numberOfEmployee: '',
  industry: '',
  websiteUrl: '',
  errors: {
    email: null,
    firstName: null,
    lastName: null,
    companyName: null,
    jobFunction: null,
    numberOfEmployee: null,
    industry: null
  }
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
  password: '',
  confirmPassword: '',
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
    password: null,
    confirmPassword: null,
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

export const registerSteps = {
  0: 'account-information',
  1: 'build-package',
  2: 'payment',
  3: 'basic-account'
};

const registerStepNames = [
  { id: registerSteps[0], step: 0, name: 'Account Information' },
  { id: registerSteps[1], step: 1, name: 'Build Package' },
  { id: registerSteps[2], step: 2, name: 'Payment / Finish' }
];

const freeRegisterStepNames = [
  { id: registerSteps[0], step: 0, name: 'Account Information' },
  { id: registerSteps[3], step: 1, name: 'Finish' }
];

// const allowedReferrers = ['www.socialhose.io', 'landing.socialhose.io'];

function Register({ actions }) {
  const { push } = useHistory();
  const { step } = useParams();
  const { search } = useLocation();
  const isMounted = useIsMounted();

  const {
    form: formBasic,
    errors: errorsBasic,
    handleChange: handleChangeBasic,
    handleValidation: handleValidationBasic,
    validateSubmit: validateSubmitBasic,
    resetForm: resetFormBasic
  } = useForm(initialBasicForm);
  const {
    form: formPlan,
    handleChange: handleChangePlan,
    resetForm: resetFormPlan
  } = useForm(initialForm);
  const {
    form: paymentForm,
    handleChange: handlePaymentForm,
    errors: paymentFormErrors,
    handleValidation: handlePaymentValidation,
    validateSubmit
  } = useForm(initialPaymentForm);

  const [planList, setPlanList] = useState([]);
  const [planLoading, setPlanLoading] = useState(true);
  const [planError, setPlanError] = useState(false);

  const [currentStep, setCurrentStep] = useState();
  const [completedSteps, setCompletedSteps] = useState({});
  const [updatingPrice, setUpdatingPrice] = useState(true);
  const [totalCost, setTotalCost] = useState(0);
  const [disabledFields, setDisabledFields] = useState([]);
  const ref = useRef();

  const searchParams = new URLSearchParams(search);

  useEffect(() => {
    const obj = {};
    const objErrs = {};
    for (let [key, value] of searchParams) {
      if (value) {
        obj[key] = value;
        objErrs[key] = false;
      }
    }
    setDisabledFields(Object.keys(obj));
    resetFormBasic({
      ...formBasic,
      ...obj,
      errors: {
        ...errorsBasic,
        ...objErrs
      }
    });

    getBillingPlans();
    setDocumentData('title', 'Register');

    return () => setDocumentData('title'); // default
  }, []);

  useEffect(() => {
    /*  const allowUser =
      document.referrer &&
      allowedReferrers.find((path) => document.referrer.includes(path));

      // only allowed referrer can access the page
    if (isLive && !allowUser) {
      push('/auth/login');
      return;
    } */

    if (!step || !Object.values(registerSteps).includes(step)) {
      push(`/auth/register/${registerSteps[0]}`);
      ref.current && (ref.current.scrollTop = 0);
      return;
    }

    if (currentStep !== step) {
      setCurrentStep(step);
      ref.current && (ref.current.scrollTop = 0);
    }
  }, [step]);

  // to update price when input changes
  useEffect(() => {
    debouncePrice(formPlan);
  }, [...Object.values(formPlan)]);

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
    []
  );

  function getBillingPlans() {
    setPlanLoading(true);
    setPlanError(false);
    getPlans().then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data || !res.data.length) {
        setPlanError(true);
        res.data && res.data.length > 0 && actions.addAlert(res.data);
        return;
      }
      setPlanLoading(false);
      setPlanList(res.data);

      const modified = { ...initialForm };
      const selectedPlan = res.data[0];
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
      resetFormPlan(modified);
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
    resetFormPlan(modified);
  }

  function getClassName(step, arr) {
    let cl = '';
    const stepDetails = arr.find((v) => v.id === currentStep);
    if (step.id === currentStep) {
      cl = 'form-wizard-step-doing';
    } else if (
      stepDetails &&
      stepDetails.step > step.step &&
      completedSteps[step.id]
    ) {
      cl = 'form-wizard-step-done';
    } else {
      cl = 'form-wizard-step-todo';
    }

    return cl;
  }

  const stepProgressNames =
    currentStep === registerSteps[3]
      ? freeRegisterStepNames
      : registerStepNames;

  return (
    <Row className="vh-100 no-gutters registration">
      <Col
        lg="8"
        md="12"
        className="vh-100 d-flex bg-white justify-content-center"
      >
        <PerfectScrollbar
          className="w-100"
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
          <div className="pointer-events-none">
            <ol className="forms-wizard">
              {stepProgressNames.map((s, i, arr) => (
                <li className={getClassName(s, arr)} key={s.id} value={i}>
                  <em>{i + 1}</em>
                  <span>{s.name}</span>
                </li>
              ))}
            </ol>
          </div>
          {planError ? (
            <div className="text-danger text-center p-4">
              <IoIosWarning className="d-block mx-auto mb-2" fontSize="32px" />
              Sorry, something went wrong.{' '}
              <Button color="link" onClick={getBillingPlans} className="p-0">
                Try again
              </Button>
            </div>
          ) : planLoading ? (
            <Loading />
          ) : null}

          {!planLoading && currentStep === registerSteps[0] && (
            <BasicDetailsPage
              form={formBasic}
              errors={errorsBasic}
              disabledFields={disabledFields}
              setCompletedSteps={setCompletedSteps}
              handleChange={handleChangeBasic}
              handleValidation={handleValidationBasic}
              validateSubmit={validateSubmitBasic}
            />
          )}
          {!planLoading && currentStep === registerSteps[1] && (
            <PlanDetailsPage
              form={formPlan}
              totalCost={totalCost}
              planList={planList}
              completedSteps={completedSteps}
              setCompletedSteps={setCompletedSteps}
              updatingPrice={updatingPrice}
              changePlan={changePlan}
              handleChange={handleChangePlan}
            />
          )}
          {!planLoading && currentStep === registerSteps[2] && (
            <PaymentDetailsPage
              form={paymentForm}
              validateSubmitBasic={validateSubmitBasic}
              planDetails={formPlan}
              completedSteps={completedSteps}
              updatingPrice={updatingPrice}
              totalCost={totalCost}
              errors={paymentFormErrors}
              handleChange={handlePaymentForm}
              handleValidation={handlePaymentValidation}
              validateSubmit={validateSubmit}
            />
          )}
          {!planLoading && currentStep === registerSteps[3] && (
            <FreeAccountPage
              form={paymentForm}
              validateSubmitBasic={validateSubmitBasic}
              errors={paymentFormErrors}
              completedSteps={completedSteps}
              handleChange={handlePaymentForm}
              handleValidation={handlePaymentValidation}
            />
          )}
        </PerfectScrollbar>
      </Col>
      <CommonSection />
    </Row>
  );
}

Register.propTypes = {
  actions: PropTypes.object.isRequired
};

export default reduxActions()(Register);
