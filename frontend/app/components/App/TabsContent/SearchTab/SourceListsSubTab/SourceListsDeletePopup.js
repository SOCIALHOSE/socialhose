import React from 'react'
import PropTypes from 'prop-types'
import { translate, Interpolate } from 'react-i18next'
import PopupLayout from '../../../../common/Popups/PopupLayout'
import { getTitle } from '../../../../../common/helper';

export class SourceListsDeletePopup extends React.Component {
  static propTypes = {
    listToEdit: PropTypes.func.isRequired,
    toggleDeleteListPopup: PropTypes.func.isRequired,
    deleteSourceList: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  onSubmit = () => {
    const { listToEdit, deleteSourceList } = this.props
    deleteSourceList(listToEdit)
  };

  render () {
    const { listToEdit, toggleDeleteListPopup } = this.props
    const value = listToEdit.name || listToEdit.title || ''

    return (
      <PopupLayout
        title='sourceListsTab.popup.deleteListTitle'
        submitText='Delete'
        onHide={toggleDeleteListPopup}
        onSubmit={this.onSubmit}
        submitColor="danger"
      >
        <Interpolate
          i18nKey='sourceListsTab.popup.deleteListDesc'
          name={getTitle(value)}
        />
      </PopupLayout>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(SourceListsDeletePopup)

