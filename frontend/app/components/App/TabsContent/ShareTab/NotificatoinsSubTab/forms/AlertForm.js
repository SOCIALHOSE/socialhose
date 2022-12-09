import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import {
  timezones,
  getCurrentTimezone
} from '../../../../../../common/Timezones';
import Select from 'react-select';
import moment from 'moment';
import DatePicker from 'react-datepicker';
import RecipientsSelect from './RecipientsSelect';
import CheckboxField from './CheckboxField';
import RadioField from './RadioField';
import BooleanRadioGroup from './BooleanRadioGroup';
import SourcesDropTarget from './sources/SourcesDropTarget';
import Sources from './sources/Sources';
import Scheduling from './scheduling/Scheduling';
import SaveAsPopup from './SaveAsPopup';
import History from './History';
import { EXTRAS } from '../../../../../../redux/modules/appState/share/forms/alertForm';
import { THEME_TYPES } from '../../../../../../redux/modules/appState/share/forms/notificationForm';
import {
  Button,
  Card,
  CardBody,
  CardTitle,
  Col,
  Container,
  CustomInput,
  Form,
  FormGroup,
  Input,
  Label
} from 'reactstrap';

export class AlertForm extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    state: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    switchShareSubScreen: PropTypes.func.isRequired
  };

  changeName = (event) => {
    this.props.actions.changeName(event.target.value);
  };

  changeSubject = (event) => {
    this.props.actions.changeSubject(event.target.value);
  };

  changeAutoSubject = () => {
    const { state, actions } = this.props;
    actions.changeAutoSubject(!state.automatedSubject);
  };

  changeRecipient = (value) => {
    this.props.actions.changeRecipients(value.split(','));
  };

  changeTimezone = (zone) => {
    this.props.actions.changeTimezone(zone.value);
  };

  toggleTimezone = () => {
    const { state, actions } = this.props;
    if (state.isEnabledTimezone) {
      actions.changeTimezone(getCurrentTimezone());
    }
    actions.toggleTimezone();
  };

  changeSendUntil = (value) => {
    const sendUntil = value ? moment(value).format('YYYY-MM-DD') : '';
    this.props.actions.changeSendUntil(sendUntil);
  };

  cancel = () => {
    this.props.switchShareSubScreen('notifications', 'tables');
  };

  create = () => {
    const { state, actions } = this.props;
    const isEdit = !!state.id;
    actions.saveAlert(isEdit);
  };

  edit = (name) => {
    const { actions } = this.props;
    actions.changeName(name);
    actions.saveAlert(false);
  };

  showSaveAsPopup = () => {
    this.props.actions.toggleSaveAsPopup();
  };

  render() {
    const { state, actions, t } = this.props;
    const isEdit = !!state.id;
    const name = state.name;

    const extract = state.content.extract;
    const userComments = state.content.showInfo.userComments;

    const sendUntil = !state.sendUntil
      ? state.sendUntil
      : moment(state.sendUntil).toDate();

    return (
      <Card className="main-card mb-3">
        <CardBody>
          <CardTitle>
            {isEdit
              ? t('notificationsTab.form.editAlert')
              : t('notificationsTab.form.createAlert')}
          </CardTitle>
          <div className="share-tab-form-container">
            <Container>
              <Form>
                <FormGroup row>
                  <Label sm={2}>{t('notificationsTab.form.name')}</Label>
                  <Col sm={10}>
                    <Input
                      type="text"
                      title={t('notificationsTab.form.nameTooltip')}
                      value={name}
                      onChange={this.changeName}
                    />
                  </Col>
                </FormGroup>

                <RecipientsSelect t={t} state={state} actions={actions} />

                <CheckboxField
                  label={t('notificationsTab.form.automatedEmail')}
                  additionalLabel={t(
                    'notificationsTab.form.automatedEmailDesc'
                  )}
                  value={state.automatedSubject}
                  onChange={this.changeAutoSubject}
                />

                {!state.automatedSubject && (
                  <FormGroup row>
                    <Label sm={2}>
                      {t('notificationsTab.form.emailSubject')}
                    </Label>
                    <Col sm={10}>
                      <Input
                        type="text"
                        title={t('notificationsTab.form.emailSubject')}
                        value={state.subject}
                        onChange={this.changeSubject}
                      />
                    </Col>
                  </FormGroup>
                )}

                <CheckboxField
                  label={t('notificationsTab.form.publish')}
                  additionalLabel={t('notificationsTab.form.publishDesc')}
                  value={state.published}
                  onChange={actions.changePublished}
                />

                <CheckboxField
                  label={t('notificationsTab.form.unsubscribe')}
                  additionalLabel={t('notificationsTab.form.unsubscribeDesc')}
                  value={state.allowUnsubscribe}
                  onChange={actions.changeAllowUnsubscribe}
                />

                <CheckboxField
                  label={t('notificationsTab.form.notifications')}
                  additionalLabel={t('notificationsTab.form.notificationsDesc')}
                  value={state.unsubscribeNotification}
                  onChange={actions.changeUnsubscribeNotification}
                />

                <FormGroup row>
                  <Label sm={2}>{t('notificationsTab.form.feeds')}</Label>
                  <Col sm={10}>
                    <Sources
                      sources={state.sources}
                      removeSource={actions.removeSource}
                      moveSource={actions.moveSource}
                    />
                    <SourcesDropTarget addSource={actions.addSource} />
                  </Col>
                </FormGroup>

                <FormGroup row>
                  <Label sm={2}>{t('notificationsTab.form.options')}</Label>
                  <Col sm={10}>
                    <FormGroup row>
                      <Col sm={2}>
                        {t('notificationsTab.form.articleExtracts')}
                      </Col>
                      <Col sm={10}>
                        <div className="d-flex">
                          <RadioField
                            label={t(
                              'notificationsTab.form.contextualExtracts'
                            )}
                            name="articleExtracts"
                            checkedValue={extract}
                            value={EXTRAS.CONTEXTUAL}
                            onChange={actions.changeExtras}
                          />

                          <RadioField
                            label={t('notificationsTab.form.startExtracts')}
                            name="articleExtracts"
                            checkedValue={extract}
                            value={EXTRAS.START}
                            onChange={actions.changeExtras}
                          />

                          <RadioField
                            label={t('notificationsTab.form.noExtracts')}
                            name="articleExtracts"
                            checkedValue={extract}
                            value={EXTRAS.NO}
                            onChange={actions.changeExtras}
                          />
                        </div>
                      </Col>
                    </FormGroup>

                    <BooleanRadioGroup
                      mainLabel={t('notificationsTab.form.highlightKeywords')}
                      name="highlightKeywords"
                      value={state.content.highlightKeywords.highlight}
                      onChange={actions.changeHighlightKeywords}
                    />

                    <BooleanRadioGroup
                      mainLabel={t('notificationsTab.form.showSourceCountry')}
                      name="showSourceCountry"
                      value={state.content.showInfo.sourceCountry}
                      onChange={actions.changeShowSourceCountry}
                    />

                    <FormGroup row>
                      <Col sm={2}>
                        {t('notificationsTab.form.showUserComments')}
                      </Col>
                      <Col sm={10}>
                        <div className="d-flex">
                          <RadioField
                            label={t('common:commonWords.Yes')}
                            name="showUserComments"
                            checkedValue={userComments}
                            value="with_author_date"
                            onChange={actions.changeShowUserComments}
                          />

                          <RadioField
                            label={t('common:commonWords.No')}
                            name="showUserComments"
                            checkedValue={userComments}
                            value="no"
                            onChange={actions.changeShowUserComments}
                          />
                        </div>
                      </Col>
                    </FormGroup>

                    <FormGroup row>
                      <Col sm={2}>{t('notificationsTab.form.layout')}</Col>
                      <Col sm={10}>
                        <div className="d-flex">
                          <RadioField
                            label={t('notificationsTab.form.enhancedHtml')}
                            name="themeType"
                            checkedValue={state.themeType}
                            value={THEME_TYPES.ENHANCED}
                            onChange={actions.changeThemeType}
                          />

                          <RadioField
                            label={t('notificationsTab.form.plainHtml')}
                            name="themeType"
                            checkedValue={state.themeType}
                            value={THEME_TYPES.PLAIN}
                            onChange={actions.changeThemeType}
                          />
                        </div>
                      </Col>
                    </FormGroup>

                    <BooleanRadioGroup
                      mainLabel={t('notificationsTab.form.sendWhenEmpty')}
                      name="sendWhenEmpty"
                      value={state.sendWhenEmpty}
                      onChange={actions.changeSendWhenEmpty}
                    />
                  </Col>
                </FormGroup>

                <FormGroup row>
                  <Label sm={2}>{t('notificationsTab.form.timezone')}</Label>
                  <Col sm={10}>
                    <Select
                      value={state.timezone}
                      options={timezones}
                      clearable={false}
                      disabled={!state.isEnabledTimezone}
                      onChange={this.changeTimezone}
                    />
                    <CustomInput
                      id="toggleTimezone"
                      type="checkbox"
                      className="mt-1"
                      checked={state.isEnabledTimezone}
                      onChange={this.toggleTimezone}
                      label={t('notificationsTab.form.change')}
                    />
                  </Col>
                </FormGroup>

                <FormGroup row>
                  <Label sm={2}>{t('notificationsTab.form.automatic')}</Label>
                  <Col sm={10}>
                    <Scheduling state={state.scheduling} actions={actions} />
                  </Col>
                </FormGroup>

                <FormGroup row>
                  <Label sm={2}>{t('notificationsTab.form.sendUntil')}</Label>
                  <Col sm={4}>
                    <DatePicker
                      className="form-control"
                      wrapperClassName="position-relative z-index-0"
                      dateFormat="yyyy-MM-dd"
                      placeholderText={t('notificationsTab.form.selectDate')}
                      selected={sendUntil}
                      minDate={moment()}
                      onChange={this.changeSendUntil}
                    />
                  </Col>
                </FormGroup>

                {isEdit && (
                  <Fragment>
                    <hr />
                    <History
                      notificationId={state.id}
                      state={state.sendHistory}
                      actions={actions}
                    />
                  </Fragment>
                )}

                <div className="text-right mb-3">
                  <Button
                    className="btn-icon"
                    color="secondary"
                    onClick={this.cancel}
                  >
                    <i className="lnr lnr-cross btn-icon-wrapper" />{' '}
                    {t('notificationsTab.form.cancel')}
                  </Button>
                  <Button
                    className="btn-icon ml-2"
                    color="success"
                    onClick={this.create}
                  >
                    <i className="lnr lnr-checkmark-circle btn-icon-wrapper" />
                    {t('notificationsTab.form.save')}
                  </Button>
                  <Button
                    className="btn-icon ml-2"
                    color="success"
                    onClick={this.showSaveAsPopup}
                  >
                    <i className="lnr lnr-checkmark-circle btn-icon-wrapper" />
                    {t('notificationsTab.form.saveAs')}
                  </Button>
                </div>
              </Form>
            </Container>

            {state.showSaveAsPopup && (
              <SaveAsPopup
                name={name}
                togglePopup={actions.toggleSaveAsPopup}
                onSubmit={this.edit}
              />
            )}
          </div>
        </CardBody>
      </Card>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(AlertForm);
