/* eslint-disable react/jsx-no-bind */
import React, { useCallback, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import {
  Col,
  Row,
  Button,
  Container,
  Card,
  CardBody,
  FormGroup,
  Label
} from 'reactstrap';
import Tooltip from 'rc-tooltip';
import Slider from 'rc-slider';
import simpleNumberLocalizer from 'react-widgets-simple-number';
import NumberPicker from 'react-widgets/lib/NumberPicker';
import { debounce } from 'lodash';

import { getPlans, updatePrice } from '../../../api/registration/registration';
import logo from '../../../images/logo/logo-small.png';
import { reduxActions } from '../../../redux/utils/connect';
import useForm from '../../common/hooks/useForm';
import useIsMounted from '../../common/hooks/useIsMounted';
import { addonFeatures, features, licenses, mediaTypes } from './PlanConstants';
import { registerSteps } from './Register';
import Loading from '../../common/Loading';
import { IoIosWarning } from 'react-icons/io';
import { useHistory } from 'react-router';
import Footer from '../../common/Footer';
import { setDocumentData } from '../../../common/helper';
import LangSettingsMenu from '../../App/AppHeader/LangSettingsMenu';

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

function CostCalculator({ actions }) {
  const { form, handleChange, resetForm } = useForm(initialForm);
  const [planList, setPlanList] = useState([]);
  const [planLoading, setPlanLoading] = useState(true);
  const [planError, setPlanError] = useState(false);
  const [totalCost, setTotalCost] = useState(' - ');
  const [updatingPrice, setUpdatingPrice] = useState(true);
  const isMounted = useIsMounted();
  const history = useHistory();

  useEffect(() => {
    getBillingPlans();

    setDocumentData('title', 'Cost Calculator');

    return () => setDocumentData('title'); // default
  }, []);

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
          setTotalCost(' - ');
          return;
        }
        setTotalCost(res.data.totalPrice);
        setUpdatingPrice(false);
      });
    }, 1000),
    []
  );

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
      resetForm(modified);
    });
  }

  const isRTL = document.documentElement.dir === 'rtl';
  return (
    <div className="cost-calc">
      <div className="text-center text-md-left m-4">
        <a
          href="http://socialhose.io/"
          rel="noopener noreferrer"
          target="_blank"
          className="d-inline-block"
        >
          <img src={logo} style={{ height: '40px' }} />
        </a>
      </div>
      <Container>
        <Row>
          <Col xs="12">
            <h1 className="cost-calc__title mb-4 mb-md-5">Cost Calculator</h1>
          </Col>
          <Col xs="12">
            <Row>
              <Col md="8" className="app-login-box">
                <Card className="w-100 mb-5">
                  <CardBody>
                    <p>
                      Bite-sized <strong>à la carte menu options</strong> with
                      monthly billing. No annual contracts. You can sign up for
                      a FREE basic plan and add options as needed. To get you
                      started, you can select one of the pre-configured plans we
                      designed for you.
                    </p>
                    <div className="divider" />
                    {planError ? (
                      <div className="text-danger text-center p-4">
                        <IoIosWarning
                          className="d-block mx-auto mb-2"
                          fontSize="32px"
                        />
                        Sorry, something went wrong.{' '}
                        <Button
                          color="link"
                          onClick={getBillingPlans}
                          className="p-0"
                        >
                          Try again
                        </Button>
                      </div>
                    ) : planLoading ? (
                      <Loading />
                    ) : null}
                    {!planLoading && !planError && (
                      <Row>
                        <Col md={12}>
                          <div className="mb-3">
                            <h6 className="font-weight-bold mb-3">
                              Pre-configured Plans
                            </h6>
                            <div className="d-flex flex-wrap justify-content-center justify-content-md-start px-3">
                              {planList.map((plan) => (
                                <Button
                                  outline
                                  key={plan.name}
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
                          <div className="divider" />
                          <div className="mb-3">
                            <h6 className="font-weight-bold mb-3">
                              Media Types
                            </h6>
                            <div className="px-3">
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
                                    handleChange(
                                      type.name,
                                      form[type.name] ? 0 : 1
                                    )
                                  }
                                >
                                  {type.title} ({type.price})
                                </Button>
                              ))}
                            </div>
                          </div>
                          <div className="divider" />

                          <div className="mb-3">
                            <h6 className="font-weight-bold mb-3">Licenses</h6>
                            <Row
                              noGutters
                              className="justify-content-center px-3"
                            >
                              {licenses.map((license) => (
                                <Col sm={6} key={license.name}>
                                  <div className="p-4 m-2 border b-radius-5 shadow-sm">
                                    <FormGroup>
                                      <div className="d-flex justify-content-between">
                                        <Label>{license.title}</Label>
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
                          <div className="divider" />
                          <Row>
                            <Col md="6">
                              <div className="mb-3">
                                <h6 className="font-weight-bold mb-3">
                                  Features
                                </h6>
                                <Row className="px-3">
                                  <Col xs="12" className="mb-3">
                                    {features.map((type) => (
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
                                        color={
                                          form[type.name] ? 'success' : 'light'
                                        }
                                        onClick={() =>
                                          handleChange(
                                            type.name,
                                            form[type.name] ? 0 : 1
                                          )
                                        }
                                      >
                                        {type.title} ({type.price})
                                      </Button>
                                    ))}
                                  </Col>
                                </Row>
                              </div>
                            </Col>
                            <Col md="6">
                              <div className="mb-3">
                                <h6 className="font-weight-bold mb-3">
                                  Add-ons
                                </h6>
                                <Row className="px-3">
                                  {addonFeatures.map((type) => (
                                    <Col xs="12" key={type.name}>
                                      <FormGroup>
                                        <Label>{type.title}</Label>
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
                        </Col>
                      </Row>
                    )}
                  </CardBody>
                </Card>
              </Col>
              <Col md="4" className="cost-calc__price-card">
                <Card className="bg-asteroid">
                  <CardBody>
                    <div className="d-flex flex-row flex-md-column text-center">
                      <div className="flex-grow-1 py-2 py-md-5">
                        <p className="text-white-50 font-weight-bold text-uppercase">
                          Billed Monthly
                        </p>
                        <h2 className="display-4 font-weight-bold text-white">
                          <span
                            className={`${updatingPrice ? 'opacity-2' : ''}`}
                          >
                            ${totalCost}
                          </span>
                          <span className="font-size-xlg text-white-50">
                            /mo
                          </span>
                        </h2>
                      </div>
                      <div className="d-flex flex-column justify-content-center align-items-center">
                        <Button
                          color="primary"
                          size="lg"
                          className="start-btn btn-wide d-block w-100 font-weight-bold mb-3"
                          onClick={() => {
                            history.push(`/auth/register/${registerSteps[0]}`);
                          }}
                        >
                          Get started
                        </Button>
                        <a
                          href="https://www.socialhose.io/en/pricing"
                          rel="noopener noreferrer"
                          target="_blank"
                        >
                          Learn more
                        </a>
                      </div>
                    </div>
                  </CardBody>
                </Card>
              </Col>
            </Row>
          </Col>
          <div className="header-dots public-lang">
            <LangSettingsMenu direction="left" />
          </div>
        </Row>
      </Container>
      <div className="p-4">
        <Footer />
      </div>
    </div>
  );
}

CostCalculator.propTypes = {
  actions: PropTypes.object.isRequired
};

export default reduxActions()(CostCalculator);
