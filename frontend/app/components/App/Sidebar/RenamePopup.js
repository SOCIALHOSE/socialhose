import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import {
  Button,
  Input,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader
} from 'reactstrap'

export class RenamePopup extends React.Component {
  static propTypes = {
    itemToRename: PropTypes.object.isRequired,
    hideRenamePopup: PropTypes.func.isRequired,
    renameFeed: PropTypes.func.isRequired,
    renameCategory: PropTypes.func.isRequired,
    addAlert: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      itemName: props.itemToRename.itemName
    }
  }

  hidePopup = () => {
    this.props.hideRenamePopup()
  }

  onSubmit = () => {
    const newName = this.state.itemName
    const {
      itemToRename,
      renameFeed,
      renameCategory,
      hideRenamePopup
    } = this.props

    switch (this.props.itemToRename.itemType) {
      case 'feed':
        renameFeed(itemToRename.itemId, newName, itemToRename.parentId)
        break
      case 'directory':
        renameCategory(itemToRename.itemId, newName, itemToRename.parentId)
        break
    }

    hideRenamePopup()
  }

  onChangeName = (e) => {
    const { value } = e.target // validation needed

    this.setState({
      itemName: value
    })
  }

  render() {
    const itemName = this.state.itemName
    const { t } = this.props

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('commonWords.Rename')}
        </ModalHeader>
        <ModalBody>
          <Label>{t('sidebarPopup.enterNamelabel')}</Label>
          <Input type="text" value={itemName} onChange={this.onChangeName} />
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('commonWords.Rename')}
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

export default translate(['common'], { wait: true })(RenamePopup)
