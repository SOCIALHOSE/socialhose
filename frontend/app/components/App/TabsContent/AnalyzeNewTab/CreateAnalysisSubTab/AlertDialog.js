import React, { Fragment, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import {
  Button,
  Form,
  FormGroup,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader
} from 'reactstrap';
import Select from 'react-select';
import { Input, Checkbox, RadioButton } from '../../../../common/FormControls';
import useForm from '../../../../common/hooks/useForm.js';
import { EXTRAS } from '../../../../../redux/modules/appState/share/forms/alertForm';
import { createAlertAPI } from '../../../../../api/analytics/createAnalytics';
import { getCurrentTimezone, timezones } from '../../../../../common/Timezones';
import { compose } from 'redux';
import reduxConnect from '../../../../../redux/utils/connect';
import translate from 'react-i18next/dist/commonjs/translate';
import { THEME_TYPES } from '../../../../../redux/modules/appState/share/forms/notificationForm';

const initialForm = {
  name: '',
  recipients: [],
  subject: '',
  automatedSubject: false,
  unsubscribeNotification: false,
  published: false,
  allowUnsubscribe: false,
  articleExtracts: EXTRAS.CONTEXTUAL,
  highlight: false,
  showSourceCountry: false,
  showUserComments: false,
  themeType: THEME_TYPES.PLAIN,
  sendWhenEmpty: false,
  timezone: getCurrentTimezone(),
  notificationType: 'alert',
  // automatic: [], // auto schedule
  // sentUntil: '',
  errors: {
    name: null
  }
};

function AlertDialog(props) {
  const { toggle, isOpen, alertCharts, actions, resetAlertChart, user } = props;
  const [loading, setLoading] = useState(false);
  const {
    form,
    handleChange,
    handleValidation,
    errors,
    validateSubmit,
    resetForm
  } = useForm(initialForm);

  function handleSubmit() {
    const obj = validateSubmit();
    if (!obj) {
      return actions.addAlert({ type: 'error', transKey: 'requiredInfo' });
    }
    setLoading(true);
    if (obj.automatedSubject) {
      delete obj.subject;
    }

    obj.sources = alertCharts.map((chart) => ({
      id: chart.id,
      type: 'chart'
    }));

    createAlertAPI(obj).then((res) => {
      if (res.error) {
        res.data
          ? actions.addAlert(res.data)
          : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
        setLoading(false);
        return;
      }
      actions.addAlert({ type: 'notice', transKey: 'alertSaved' });
      setLoading(false);
      toggle();
      resetForm();
      resetAlertChart();
    });
  }

  useEffect(() => {
    if (form.recipients && user.recipient && user.recipient.id) {
      handleChange('recipients', [user.recipient.id]);
    }

    return () => resetForm();
  }, []);

  return (
    <Modal isOpen={isOpen} toggle={toggle} backdrop="static" size="lg">
      <ModalHeader toggle={toggle}>Create Alert</ModalHeader>
      <ModalBody>
        <Form>
          <FormGroup>
            <Label>Selected Charts</Label>
            <div className="b-radius-5 bg-light p-2">
              {alertCharts.map((chart, i, arr) => (
                <Fragment key={chart.name}>
                  <span className="d-inline-block mr-1">
                    {chart.name}
                    {arr.length - 1 !== i ? ', ' : ''}
                  </span>
                </Fragment>
              ))}
            </div>
          </FormGroup>
          <Input
            name="name"
            title="Name"
            required
            value={form.name}
            error={errors.name}
            handleChange={handleChange}
            handleValidation={handleValidation}
          />
          <Checkbox
            name="automatedSubject"
            title="Automated Subject"
            description="Use automated email subject based on the feeds"
            value={form.automatedSubject}
            error={errors.automatedSubject}
            handleChange={handleChange}
          />
          {!form.automatedSubject && (
            <Input
              name="subject"
              title="Email Subject"
              value={form.subject}
              error={errors.subject}
              handleChange={handleChange}
              handleValidation={handleValidation}
            />
          )}
          <Checkbox
            name="published"
            title="Publish"
            description="Alerts and Newsletters that are Published are available for other users to subscribe"
            value={form.published}
            error={errors.publish}
            handleChange={handleChange}
          />
          <Checkbox
            name="allowUnsubscribe"
            title="Unsubscribe Link"
            description="Allow recipients to unsubscribe from Alert"
            value={form.allowUnsubscribe}
            error={errors.allowUnsubscribe}
            handleChange={handleChange}
          />
          <Checkbox
            name="unsubscribeNotification"
            title="Notifications"
            description="Notify creator when recipients unsubscribe"
            value={form.unsubscribeNotification}
            error={errors.unsubscribeNotification}
            handleChange={handleChange}
          />
          <FormGroup className="radio-options">
            <Label>Options</Label>
            <RadioButton
              name="articleExtracts"
              title="Article Extracts"
              formClass="mb-0"
              options={[
                { label: 'Contextual extract', value: EXTRAS.CONTEXTUAL },
                { label: 'Start of text extract', value: EXTRAS.START },
                { label: 'No article extract', value: EXTRAS.NO }
              ]}
              inline
              value={form.articleExtracts}
              error={errors.articleExtracts}
              handleChange={handleChange}
            />
            <RadioButton
              name="highlight"
              title="Highlight Keywords"
              formClass="mb-0"
              options={[
                { label: 'Yes', value: true },
                { label: 'No', value: false }
              ]}
              inline
              value={form.highlight}
              error={errors.highlight}
              handleChange={handleChange}
            />
            <RadioButton
              name="showSourceCountry"
              title="Show Source Country"
              formClass="mb-0"
              options={[
                { label: 'Yes', value: true },
                { label: 'No', value: false }
              ]}
              inline
              value={form.showSourceCountry}
              error={errors.showSourceCountry}
              handleChange={handleChange}
            />
            <RadioButton
              name="showUserComments"
              title="Show User Comments"
              formClass="mb-0"
              options={[
                { label: 'Yes', value: true },
                { label: 'No', value: false }
              ]}
              inline
              value={form.showUserComments}
              error={errors.showUserComments}
              handleChange={handleChange}
            />
            <RadioButton
              name="themeType"
              title="Layout"
              formClass="mb-0"
              options={[
                { label: 'Enhanced HTML', value: THEME_TYPES.ENHANCED },
                { label: 'Plain HTML', value: THEME_TYPES.PLAIN }
              ]}
              inline
              value={form.themeType}
              error={errors.themeType}
              handleChange={handleChange}
            />
            <RadioButton
              name="sendWhenEmpty"
              title="Send When Empty"
              formClass="mb-0"
              options={[
                { label: 'Yes', value: true },
                { label: 'No', value: false }
              ]}
              inline
              value={form.sendWhenEmpty}
              error={errors.sendWhenEmpty}
              handleChange={handleChange}
            />
          </FormGroup>
          <FormGroup>
            <Label>Timezone</Label>
            <Select
              className="timezone-select"
              value={form.timezone}
              options={timezones}
              clearable={false}
              onChange={function (v) {
                handleChange('timezone', v.value);
              }}
            />
          </FormGroup>
          {/* <FormGroup>
            <Label>Automatic</Label>
            <Scheduling state={state.scheduling} actions={actions} />
          </FormGroup> */}
        </Form>
      </ModalBody>
      <ModalFooter>
        <Button color="link" onClick={toggle}>
          Cancel
        </Button>
        <Button color="primary" disabled={loading} onClick={handleSubmit}>
          {loading ? 'Loading...' : 'Submit'}
        </Button>
      </ModalFooter>
    </Modal>
  );
}

AlertDialog.propTypes = {
  toggle: PropTypes.func,
  resetAlertChart: PropTypes.func,
  isOpen: PropTypes.bool,
  alertCharts: PropTypes.array,
  user: PropTypes.object,
  actions: PropTypes.object
};

const applyDecorators = compose(
  reduxConnect('user', ['common', 'auth', 'user']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(AlertDialog);
