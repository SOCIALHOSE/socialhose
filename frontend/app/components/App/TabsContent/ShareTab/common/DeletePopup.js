import React from 'react'
import PropTypes from 'prop-types'
import { translate, Interpolate } from 'react-i18next'
import { Button, Modal, ModalHeader, ModalBody, ModalFooter } from 'reactstrap'

export class DeletePopup extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired,
    idsToDelete: PropTypes.array.isRequired,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  }

  hidePopup = () => {
    const { actions } = this.props
    actions.cancelDelete()
  }

  onSubmit = () => {
    const { actions, idsToDelete } = this.props
    actions.deleteItems(idsToDelete)
  }

  render() {
    const { t, idsToDelete, deleteSingleText, deleteMultipleText } = this.props
    const length = Object.keys(idsToDelete).length

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('common:commonWords.Confirm')}
        </ModalHeader>
        <ModalBody>
          <p>
            {length === 1 ? (
              t('tabsContent:deletePopup.' + deleteSingleText)
            ) : (
              <Interpolate
                i18nKey={'tabsContent:deletePopup.' + deleteMultipleText}
                count={length}
              />
            )}
          </p>
        </ModalBody>

        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('common:commonWords.Cancel')}
          </Button>
          <Button color="danger" onClick={this.onSubmit}>
            {t('common:commonWords.Delete')}
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

export default translate(['common', 'tabsContent'], { wait: true })(DeletePopup)
