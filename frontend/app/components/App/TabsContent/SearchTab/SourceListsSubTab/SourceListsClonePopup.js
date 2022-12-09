import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import PopupLayout from '../../../../common/Popups/PopupLayout'
import { FormGroup, Input, Label } from 'reactstrap'

export class SourceListsClonePopup extends React.Component {
  static propTypes = {
    listToEdit: PropTypes.func.isRequired,
    toggleCloneListPopup: PropTypes.func.isRequired,
    cloneSourceList: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      name:
        props.listToEdit && props.listToEdit.name
          ? `${props.listToEdit.name} (copy)`
          : ''
    }
  }

  handleChange = (e) => {
    const { value } = e.target
    this.setState({
      name: value
    })
  }

  onSubmit = () => {
    const { listToEdit, cloneSourceList } = this.props
    cloneSourceList({
      id: listToEdit.id,
      name: this.state.name
    })
  }

  render() {
    const { toggleCloneListPopup, t } = this.props

    return (
      <PopupLayout
        title="Clone"
        submitText="sourceListsTab.popup.cloneListSubmitBtn"
        onHide={toggleCloneListPopup}
        onSubmit={this.onSubmit}
      >
        <FormGroup>
          <Label>{t('sourceListsTab.popup.renameListTitle')}</Label>
          <Input
            type="text"
            value={this.state.name}
            onChange={this.handleChange}
          />
        </FormGroup>
      </PopupLayout>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  SourceListsClonePopup
)
