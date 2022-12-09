import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Button, Modal, ModalHeader, ModalBody, ModalFooter } from 'reactstrap'

class EmailConfirmPopup extends React.Component {
  static propTypes = {
    hidePopup: PropTypes.func.isRequired,
    hideEmailPopup: PropTypes.func.isRequired,
    sendDocumentsByEmail: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  hidePopup = () => {
    this.props.hidePopup()
  }

  onSubmit = () => {
    this.props.sendDocumentsByEmail()
    this.hidePopup()
    this.props.hideEmailPopup()
  }

  render() {
    const { t } = this.props

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('common:commonWords.Confirm')}
        </ModalHeader>
        <ModalBody>
          <p>{t('searchTab.emailPopup.sendConfirmWithoutSubject')}</p>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('searchTab.emailPopup.dontSend')}
          </Button>
          <Button color="warning" onClick={this.onSubmit}>
            {t('searchTab.emailPopup.sendAnyway')}
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  EmailConfirmPopup
)
