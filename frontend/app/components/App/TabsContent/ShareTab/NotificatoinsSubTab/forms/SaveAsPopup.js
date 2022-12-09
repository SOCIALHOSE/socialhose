import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import {
  Button,
  Modal,
  ModalHeader,
  ModalBody,
  ModalFooter,
  Label,
  FormGroup,
  Input
} from 'reactstrap'

export class SaveAsPopup extends React.Component {
  static propTypes = {
    name: PropTypes.string.isRequired,
    togglePopup: PropTypes.func.isRequired,
    onSubmit: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      name: `${props.name} (copy)`
    }
  }

  hidePopup = () => {
    this.props.togglePopup()
  }

  onSubmit = () => {
    this.props.onSubmit(this.state.name)
    this.hidePopup()
  }

  handleChange = (e) => {
    const { name, value } = e.target
    this.setState({ [name]: value })
  }

  render() {
    const { t } = this.props

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('notificationsTab.popup.saveAs')}
        </ModalHeader>
        <ModalBody>
          <FormGroup>
            <Label>{t('notificationsTab.popup.saveAsPlaceholder')}</Label>
            <Input
              type="text"
              name="name"
              value={this.state.name}
              onChange={this.handleChange}
            />
          </FormGroup>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('common:commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('notificationsTab.popup.save')}
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(SaveAsPopup)
