/* eslint-disable react/jsx-no-bind */
import React, { useEffect } from 'react';
import PropTypes from 'prop-types';
import Tooltip from 'rc-tooltip';
import Slider from 'rc-slider';
import { Link, useHistory } from 'react-router-dom';
import { Col, Row, Button, Form, FormGroup, Label } from 'reactstrap';
import { addonFeatures, features, licenses, mediaTypes } from './PlanConstants';
import { registerSteps } from './Register';

import simpleNumberLocalizer from 'react-widgets-simple-number';
import NumberPicker from 'react-widgets/lib/NumberPicker';

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

function PlanDetailsPage({
  changePlan,
  form = {},
  handleChange,
  updatingPrice,
  totalCost,
  completedSteps,
  setCompletedSteps,
  planList
}) {
  const { push, replace } = useHistory();

  useEffect(() => {
    if (!completedSteps[registerSteps[0]]) {
      replace(`/auth/register/${registerSteps[0]}`);
    }
  }, []);

  function nextStep() {
    if (!updatingPrice) {
      setCompletedSteps((prev) => ({ ...prev, [registerSteps[1]]: true }));
      push(`/auth/register/${registerSteps[2]}`);
    }
  }

  const isRTL = document.documentElement.dir === 'rtl';
  return (
    <Col lg="9" md="10" sm="12" className="mx-auto app-login-box mt-4 mb-5">
      <p className="text-muted">
        If you know EXACTLY what you want, you can build your package below.
        Otherwise, you can sign up for a Free Basic Account{' '}
        <Link to={`/auth/register/${registerSteps[3]}`}>here</Link>.
      </p>
      <Row className="divider" />
      <Form>
        <Row>
          <Col md={12}>
            <div className="mb-3">
              <h6 className="font-weight-bold mb-3">Pre-configured Plans</h6>
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
            <Row className="divider" />
            <div className="mb-3">
              <h6 className="font-weight-bold mb-3">Media Types</h6>
              <div className="px-3">
                {mediaTypes.map((type) => (
                  <Button
                    key={type.name}
                    size="lg"
                    type="button"
                    title={
                      form[type.name] ? 'Click to deselect' : 'Click to select'
                    }
                    outline={!form[type.name]}
                    className="btn-pill mb-2 mr-2"
                    color={form[type.name] ? 'success' : 'light'}
                    onClick={() =>
                      handleChange(type.name, form[type.name] ? 0 : 1)
                    }
                  >
                    {type.title} ({type.price})
                  </Button>
                ))}
              </div>
            </div>
            <Row className="divider" />

            <div className="mb-3">
              <h6 className="font-weight-bold mb-3">Licenses</h6>
              <Row noGutters className="justify-content-center px-3">
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
                          onChange={(val) => handleChange(license.name, val)}
                        />
                      </FormGroup>
                    </div>
                  </Col>
                ))}
              </Row>
            </div>
            <Row className="divider" />
            <Row>
              <Col md="6">
                <div className="mb-3">
                  <h6 className="font-weight-bold mb-3">Features</h6>
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
                          color={form[type.name] ? 'success' : 'light'}
                          onClick={() =>
                            handleChange(type.name, form[type.name] ? 0 : 1)
                          }
                        >
                          {type.title} ({type.price})
                        </Button>
                      ))}
                      {/*  <div className="pl-2">
                    {features.map((type) =>
                      form[type.name] ? (
                        <p
                          key={type.name}
                          className="font-size-xs text-muted mb-1"
                        >
                          {type.desc}
                        </p>
                      ) : null
                    )}
                  </div> */}
                    </Col>
                  </Row>
                </div>
              </Col>
              <Col md="6">
                <div className="mb-3">
                  <h6 className="font-weight-bold mb-3">Add-ons</h6>
                  <Row className="px-3">
                    {addonFeatures.map((type) => (
                      <Col xs="12" key={type.name}>
                        <FormGroup>
                          <Label>{type.title}</Label>
                          <NumberPicker
                            {...type.props}
                            value={form[type.name]}
                            onChange={(val) => handleChange(type.name, val)}
                          />
                        </FormGroup>
                      </Col>
                    ))}
                  </Row>
                </div>
              </Col>
            </Row>
            <Row className="divider" />
            <div className="widget-content total-price">
              <div className="widget-content-wrapper justify-content-start justify-content-md-end mr-5">
                <div className="widget-content-left">
                  <div className="widget-heading">Total Cost</div>
                  <div className="widget-subheading">Monthly</div>
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
        <Row className="divider" />
        <div className="d-flex justify-content-sm-between flex-column-reverse flex-sm-row flex-wrap">
          <Link
            to={`/auth/register/${registerSteps[0]}`}
            className={updatingPrice ? 'pointer-events-none' : ''}
          >
            <Button
              type="button"
              color="secondary"
              size="lg"
              disabled={updatingPrice}
            >
              Back
            </Button>
          </Link>
          <div className="d-flex flex-column-reverse text-center d-sm-block">
            <Link to={`/auth/register/${registerSteps[3]}`}>
              <Button type="button" color="link" size="lg">
                Choose Plan Later
              </Button>
            </Link>
            <Button
              type="button"
              disabled={updatingPrice}
              onClick={nextStep}
              className="btn-wide"
              color="primary"
              size="lg"
            >
              Continue to Payment
            </Button>
          </div>
        </div>
      </Form>
    </Col>
  );
}

PlanDetailsPage.propTypes = {
  changePlan: PropTypes.func,
  form: PropTypes.object,
  handleChange: PropTypes.func,
  completedSteps: PropTypes.object,
  planList: PropTypes.array,
  setCompletedSteps: PropTypes.func,
  updatingPrice: PropTypes.bool,
  totalCost: PropTypes.oneOfType([PropTypes.number, PropTypes.string])
};

export default React.memo(PlanDetailsPage);
