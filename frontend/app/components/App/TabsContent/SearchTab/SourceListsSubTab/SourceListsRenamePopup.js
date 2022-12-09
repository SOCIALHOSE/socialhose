import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import PopupLayout from '../../../../common/Popups/PopupLayout'
import { FormGroup, Input, Label } from 'reactstrap'

export class SourceListsRenamePopup extends React.Component {
  static propTypes = {
    listToEdit: PropTypes.func.isRequired,
    toggleRenameListPopup: PropTypes.func.isRequired,
    renameSourceList: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      name: (props.listToEdit && props.listToEdit.name) || ''
    }
  }

  handleChange = (e) => {
    const { value } = e.target
    this.setState({
      name: value
    })
  }

  onSubmit = () => {
    const { listToEdit, renameSourceList } = this.props
    const data = {
      id: listToEdit.id,
      name: this.state.name
    }

    renameSourceList(data, listToEdit.name)
  }

  render() {
    const { toggleRenameListPopup, t } = this.props

    return (
      <PopupLayout
        title="Rename"
        submitText="sourceListsTab.popup.renameListSubmitBtn"
        onHide={toggleRenameListPopup}
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
  SourceListsRenamePopup
)
