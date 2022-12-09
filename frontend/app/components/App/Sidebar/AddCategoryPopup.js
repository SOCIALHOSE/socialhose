import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import {
  Button,
  Modal,
  ModalHeader,
  ModalBody,
  Label,
  Input,
  ModalFooter
} from 'reactstrap';

export class AddCategoryPopup extends React.Component {
  state = {
    folderName: ''
  };

  static propTypes = {
    parentId: PropTypes.number.isRequired,
    hideAddCategoryPopup: PropTypes.func.isRequired,
    addCategory: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  onChangeName = (e) => {
    const { value } = e.target; // need validation
    this.setState({ folderName: value });
  };

  hidePopup = () => {
    this.props.hideAddCategoryPopup();
  };

  onSubmit = () => {
    const { folderName } = this.state;
    this.props.addCategory(folderName, this.props.parentId);
    this.props.hideAddCategoryPopup();
  };

  render() {
    const { t } = this.props;
    const { folderName } = this.state;

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('sidebarPopup.addFolderBtn')}
        </ModalHeader>
        <ModalBody>
          <Label>{t('sidebarPopup.enterFolderName')}</Label>
          <Input type="text" value={folderName} onChange={this.onChangeName} />
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('sidebarPopup.addFolderBtn')}
          </Button>
        </ModalFooter>
      </Modal>
    );
  }
}

export default translate(['common'], { wait: true })(AddCategoryPopup);
