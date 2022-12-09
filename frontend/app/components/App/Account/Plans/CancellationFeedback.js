import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import useForm from '../../../common/hooks/useForm';
import {
  ListGroupItem,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row,
  Form,
  Button,
  Col,
  ListGroup,
  Label
} from 'reactstrap';
import { Checkbox, Input } from '../../../common/FormControls';
import { cancelPlan, cancelPlanHubspot } from '../../../../api/plans/userPlans';
import { planRoutes } from './UserPlans';

const formParams = {
  rs1: 1,
  rs2: 2,
  rs3: 3,
  rs4: 4,
  rs5: 5,
  rs6: 'Other'
};

const initForm = {
  [formParams.rs1]: false,
  [formParams.rs2]: false,
  [formParams.rs3]: false,
  [formParams.rs4]: false,
  [formParams.rs5]: false,
  [formParams.rs6]: false,
  Other: false,
  content: '',
  email: '',
  subject: 'Cancellation',
  errors: {
    email: null
  }
};

function CancellationFeedback({ t, actions, isOpen = false, toggle, user }) {
  const {
    form,
    handleChange,
    handleValidation,
    validateSubmit,
    errors
  } = useForm(initForm);
  const [cancelLoading, setCancelLoading] = useState(false);
  const [reasonError, setReasonError] = useState('');

  useEffect(() => {
    handleChange('email', user.email);
  }, [user.email]);

  useEffect(() => {
    if (Object.values(formParams).some((v) => form[v])) {
      setReasonError('');
    }
  }, [...Object.values(form)]);

  function cancelSubscription() {
    const obj = validateSubmit();
    if (!obj) {
      return actions.addAlert({ type: 'error', transKey: 'requiredInfo' });
    } else if (!Object.values(formParams).some((v) => obj[v])) {
      setReasonError(t('plans.currentPlan.cancelModal.reasonSelect'));
      return;
    }

    setCancelLoading(true);
    cancelPlan().then((res) => {
      if (res.error) {
        res.data
          ? actions.addAlert(res.data)
          : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
        setCancelLoading(false);
        return;
      }

      cancelPlanHubspot({ ...obj });

      actions.addAlert({
        type: 'notice',
        transKey: 'cancelledSubscription'
      });

      // refresh page on success and move to active plan details
      setTimeout(() => {
        window.location.pathname = `/app/plans/${planRoutes.current}`;
      }, 1000);
    });
  }

  return (
    <div>
      <Modal size="lg" backdrop="static" isOpen={isOpen} toggle={toggle}>
        <ModalHeader toggle={toggle}>
          {t('plans.currentPlan.cancelModal.header')}
        </ModalHeader>
        <ModalBody>
          <Row>
            <Col md={6} className="mb-3">
              <p className="mb-3">
                {t('plans.currentPlan.cancelModal.line1', {
                  firstName: user.firstName
                })}
              </p>
              <p className="mb-2">{t('plans.currentPlan.cancelModal.line2')}</p>
              <ListGroup className="text-muted">
                <ListGroupItem>
                  {t('plans.currentPlan.cancelModal.warn1')}
                </ListGroupItem>
                <ListGroupItem>
                  {t('plans.currentPlan.cancelModal.warn2')}
                </ListGroupItem>
                <ListGroupItem>
                  {t('plans.currentPlan.cancelModal.warn3')}
                </ListGroupItem>
                <ListGroupItem>
                  {t('plans.currentPlan.cancelModal.warn4')}
                </ListGroupItem>
              </ListGroup>
            </Col>
            <Col md={6} className="mb-3">
              <Form>
                <p className="mb-4">
                  {t('plans.currentPlan.cancelModal.feedbackPara')}
                </p>
                <div>
                  <Label className="d-inline-block mb-2">
                    {t('plans.currentPlan.cancelModal.reasonCancellation')}
                    <span className="text-danger">*</span>
                  </Label>
                  <div className="pl-3 mb-3">
                    <Checkbox
                      hideTitle
                      name={formParams.rs1}
                      formGroupClass="mb-0"
                      title={t('plans.currentPlan.cancelModal.noNeeds')}
                      description={t('plans.currentPlan.cancelModal.noNeeds')}
                      value={form[formParams.rs1]}
                      error={errors[formParams.rs1]}
                      handleChange={handleChange}
                    />
                    <Checkbox
                      hideTitle
                      name={formParams.rs2}
                      formGroupClass="mb-0"
                      title={t('plans.currentPlan.cancelModal.tooNoisy')}
                      description={t('plans.currentPlan.cancelModal.tooNoisy')}
                      value={form[formParams.rs2]}
                      error={errors[formParams.rs2]}
                      handleChange={handleChange}
                    />
                    <Checkbox
                      hideTitle
                      name={formParams.rs3}
                      formGroupClass="mb-0"
                      title={t('plans.currentPlan.cancelModal.confusing')}
                      description={t('plans.currentPlan.cancelModal.confusing')}
                      value={form[formParams.rs3]}
                      error={errors[formParams.rs3]}
                      handleChange={handleChange}
                    />
                    <Checkbox
                      hideTitle
                      name={formParams.rs4}
                      formGroupClass="mb-0"
                      title={t('plans.currentPlan.cancelModal.expensive')}
                      description={t('plans.currentPlan.cancelModal.expensive')}
                      value={form[formParams.rs4]}
                      error={errors[formParams.rs4]}
                      handleChange={handleChange}
                    />
                    <Checkbox
                      hideTitle
                      name={formParams.rs5}
                      formGroupClass="mb-0"
                      title={t('plans.currentPlan.cancelModal.covid')}
                      description={t('plans.currentPlan.cancelModal.covid')}
                      value={form[formParams.rs5]}
                      error={errors[formParams.rs5]}
                      handleChange={handleChange}
                    />
                    <Checkbox
                      hideTitle
                      name={formParams.rs6}
                      formGroupClass="mb-0"
                      title={t('plans.currentPlan.cancelModal.other')}
                      description={t('plans.currentPlan.cancelModal.other')}
                      value={form[formParams.rs6]}
                      error={errors[formParams.rs6]}
                      handleChange={handleChange}
                    />
                    <span className="text-danger">{reasonError}</span>
                  </div>
                </div>
                <Input
                  name="content"
                  title={t('plans.currentPlan.cancelModal.tellMore')}
                  type="textarea"
                  value={form.content}
                  error={errors.content}
                  handleChange={handleChange}
                  handleValidation={handleValidation}
                />
              </Form>
            </Col>
          </Row>
        </ModalBody>
        <ModalFooter>
          <Button color="link" onClick={toggle}>
            {t('plans.currentPlan.cancelModal.undoBtn')}
          </Button>
          <Button
            color="danger"
            disabled={cancelLoading}
            onClick={cancelSubscription}
          >
            {cancelLoading
              ? t('plans.currentPlan.cancelModal.loadingBtn')
              : t('plans.currentPlan.cancelModal.cancelSubscriptionBtn')}
          </Button>
        </ModalFooter>
      </Modal>
    </div>
  );
}

CancellationFeedback.propTypes = {
  t: PropTypes.func,
  actions: PropTypes.object,
  isOpen: PropTypes.bool,
  toggle: PropTypes.func,
  user: PropTypes.object
};

export default CancellationFeedback;
