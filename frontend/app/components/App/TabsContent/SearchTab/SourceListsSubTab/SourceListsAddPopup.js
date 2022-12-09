import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import PopupLayout from '../../../../common/Popups/PopupLayout'
import { FormGroup, Input, Label } from 'reactstrap'

export class SourceListsAddPopup extends React.Component {
  static propTypes = {
    toggleAddListPopup: PropTypes.func.isRequired,
    addSourceList: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  state = {
    name: ''
  }

  onSubmit = () => {
    const { addSourceList } = this.props
    addSourceList(this.state.name)
  }

  handleChange = (e) => {
    const { value } = e.target
    this.setState({ name: value })
  }

  render() {
    const { toggleAddListPopup, t } = this.props

    return (
      <PopupLayout
        title="Add a List"
        submitText="Submit"
        onHide={toggleAddListPopup}
        onSubmit={this.onSubmit}
      >
        <div>
          <FormGroup>
            <Label>{t('sourceListsTab.popup.enterListName')}</Label>
            <Input
              type="text"
              value={this.state.name}
              onChange={this.handleChange}
            />
          </FormGroup>
        </div>
      </PopupLayout>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  SourceListsAddPopup
)
