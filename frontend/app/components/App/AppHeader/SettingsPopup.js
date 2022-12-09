import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import {
  Button,
  Label,
  Input,
  FormGroup,
  Modal,
  ModalHeader,
  ModalBody,
  ModalFooter
} from 'reactstrap';

export class SettingsPopup extends React.Component {
  static propTypes = {
    hidePopup: PropTypes.func.isRequired,
    setErrorMsg: PropTypes.func.isRequired,
    changePassword: PropTypes.func.isRequired,
    errorMsg: PropTypes.string,
    t: PropTypes.func.isRequired
  };

  constructor() {
    super();
    this.state = {
      oldPassword: '',
      newPassword: '',
      confirmPassword: ''
    };
  }

  hidePopup = () => {
    this.props.hidePopup();
    this.props.setErrorMsg(null);
  };

  onSubmit = () => {
    const { t } = this.props;
    const { oldPassword, newPassword, confirmPassword } = this.state;

    // need more validations
    if (!oldPassword || !newPassword || !confirmPassword) {
      return this.props.setErrorMsg(t('userSettings.enterRequiredFields'));
    }

    if (newPassword !== confirmPassword) {
      return this.props.setErrorMsg(t('userSettings.passwordsNotMatched'));
    }

    if (oldPassword && newPassword) {
      this.props.changePassword(newPassword, oldPassword);
    }
  };

  handleChange = (e) => {
    const { name, value } = e.target;
    this.setState({ [name]: value });
  };

  render() {
    const { t, errorMsg } = this.props;

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('userSettings.changePassword')}
        </ModalHeader>
        <ModalBody>
          <FormGroup>
            <Label>
              {t('userSettings.enterOldPassword')}
              <span className="text-danger">*</span>
            </Label>
            <Input
              type="password"
              name="oldPassword"
              onChange={this.handleChange}
            />
          </FormGroup>
          <FormGroup>
            <Label>
              {t('userSettings.enterNewPassword')}
              <span className="text-danger">*</span>
            </Label>
            <Input
              type="password"
              name="newPassword"
              onChange={this.handleChange}
            />
          </FormGroup>
          <FormGroup>
            <Label>
              {t('userSettings.retypeNewPassword')}
              <span className="text-danger">*</span>
            </Label>
            <Input
              type="password"
              name="confirmPassword"
              onChange={this.handleChange}
            />
          </FormGroup>

          <p className="text-danger">{errorMsg}</p>
        </ModalBody>

        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('common:commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('userSettings.changePassword')}
          </Button>
        </ModalFooter>
      </Modal>
    );
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  SettingsPopup
);
