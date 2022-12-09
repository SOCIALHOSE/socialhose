import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Button, Modal, ModalHeader, ModalBody, ModalFooter } from 'reactstrap';

export class DeletePopup extends React.Component {
  static propTypes = {
    itemToDelete: PropTypes.object.isRequired,
    hideDeletePopup: PropTypes.func.isRequired,
    deleteFeed: PropTypes.func.isRequired,
    deleteCategory: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  hidePopup = () => {
    this.props.hideDeletePopup();
  };

  onSubmit = () => {
    const {
      itemToDelete,
      deleteCategory,
      deleteFeed,
      hideDeletePopup
    } = this.props;
    switch (this.props.itemToDelete.itemType) {
      case 'feed':
        deleteFeed(itemToDelete.itemId, itemToDelete.parentId);
        break;
      case 'directory':
        deleteCategory(itemToDelete.itemId);
        break;
    }
    hideDeletePopup();
  };

  render() {
    const itemName = this.props.itemToDelete.itemName;
    const itemType = this.props.itemToDelete.itemType;
    const { t } = this.props;

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('commonWords.Confirm')}
        </ModalHeader>
        <ModalBody>
          <p>
            {t('messages.deleteMessage')} {itemType + ' "' + itemName + '"'}
          </p>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('commonWords.Cancel')}
          </Button>
          <Button color="danger" onClick={this.onSubmit}>
            {t('commonWords.Delete')}
          </Button>
        </ModalFooter>
      </Modal>
    );
  }
}

export default translate(['common'], { wait: true })(DeletePopup);
