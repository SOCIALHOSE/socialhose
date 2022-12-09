import React from 'react';
import PropTypes from 'prop-types';
import { Button, Col, Form, Row } from 'reactstrap';
import { useHistory, Link } from 'react-router-dom';

import { registerSteps } from './Register';
import { reduxActions } from '../../../redux/utils/connect';
import { Input } from '../../common/FormControls';
import { industryList } from './PlanConstants';

function BasicDetailsPage({
  form,
  disabledFields,
  handleChange,
  handleValidation,
  validateSubmit,
  setCompletedSteps,
  actions,
  errors
}) {
  const history = useHistory();

  function nextStep() {
    const obj = validateSubmit();
    if (!obj) {
      return actions.addAlert({
        type: 'error',
        transKey: 'requiredInfo'
      });
    }

    setCompletedSteps((prev) => ({ ...prev, [registerSteps[0]]: true }));
    history.push(`/auth/register/${registerSteps[1]}`);
  }

  return (
    <Col lg="9" md="10" sm="12" className="mx-auto app-login-box mt-4 mb-5">
      <Form>
        <Row>
          <Col md={12}>
            <p className="text-muted mb-3">
              NOTE: You will not be asked to enter any credit card information
              if you opt for the Free Basic Account.
            </p>
          </Col>
          <Col md={12}>
            <Input
              name="email"
              title="Email"
              type="email"
              required
              disabled={disabledFields.includes('email')}
              value={form.email}
              error={errors.email}
              handleChange={
                !disabledFields.includes('email') ? handleChange : undefined
              }
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="firstName"
              title="First Name"
              type="text"
              required
              disabled={disabledFields.includes('firstName')}
              value={form.firstName}
              error={errors.firstName}
              handleChange={
                !disabledFields.includes('firstName') ? handleChange : undefined
              }
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="lastName"
              title="Last Name"
              type="text"
              required
              disabled={disabledFields.includes('lastName')}
              value={form.lastName}
              error={errors.lastName}
              handleChange={
                !disabledFields.includes('lastName') ? handleChange : undefined
              }
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={6}>
            <Input
              name="companyName"
              title="Company"
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
              title="Job Function"
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
              title="Employees"
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
              title="Industry"
              type="select"
              required
              options={industryList}
              disabled={disabledFields.includes('industry')}
              value={form.industry}
              error={errors.industry}
              handleChange={
                !disabledFields.includes('industry') ? handleChange : undefined
              }
              handleValidation={handleValidation}
            />
          </Col>
          <Col md={12}>
            <Input
              name="websiteUrl"
              title="Website URL"
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
        </Row>
        <Row className="divider" />
        <div className="d-flex align-items-center flex-wrap">
          <h6 className="mt-3">
            Already have an account?{' '}
            <Link className="text-primary" to="/auth/login">
              Sign in
            </Link>
          </h6>
          <Button
            type="button"
            color="primary"
            size="lg"
            className="btn-wide ml-auto"
            onClick={nextStep}
          >
            Next
          </Button>
        </div>
      </Form>
    </Col>
  );
}

export const employeesOptions = [
  {
    label: '1-5',
    value: '1-5'
  },
  {
    label: '5-25',
    value: '5-25'
  },
  {
    label: '25-50',
    value: '25-50'
  },
  {
    label: '50-100',
    value: '50-100'
  },
  {
    label: '100-500',
    value: '100-500'
  },
  {
    label: '500-1000',
    value: '500-1000'
  },
  {
    label: '1000+',
    value: '1000+'
  }
];

BasicDetailsPage.propTypes = {
  form: PropTypes.object,
  disabledFields: PropTypes.array,
  errors: PropTypes.object,
  handleChange: PropTypes.func,
  validateSubmit: PropTypes.func,
  setCompletedSteps: PropTypes.func,
  handleValidation: PropTypes.func,
  actions: PropTypes.object
};

export default React.memo(reduxActions()(BasicDetailsPage));
