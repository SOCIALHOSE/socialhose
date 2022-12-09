import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import SourceListsAddPopup from './SourceListsAddPopup'
import SourceListsDeletePopup from './SourceListsDeletePopup'
import SourceListsRenamePopup from './SourceListsRenamePopup'
import SourceListsClonePopup from './SourceListsClonePopup'
import SourceListsTable from './SourceListsTable'
import { Button, CustomInput } from 'reactstrap'

export class SourceLists extends React.Component {
  static propTypes = {
    sourceListsState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  }

  onGlobalOnlyClick = () => {
    const { actions, sourceListsState } = this.props
    actions.toggleOnlyGlobal()
    const params = {
      page: sourceListsState.page,
      limit: sourceListsState.limit,
      onlyShared: !sourceListsState.onlyGlobal,
      sort: {
        field: sourceListsState.sortByField,
        direction: sourceListsState.sortDirection
      }
    }
    actions.getMainSourceLists(params)
  }

  render() {
    const { t, sourceListsState, actions } = this.props
    const {
      isAddListPopupVisible,
      isDeletePopupVisible,
      isRenameListPopupVisible,
      isCloneListPopupVisible,
      listToEdit
    } = sourceListsState

    return (
      <div className="source-lists-tab">
        <div className="d-flex justify-content-between align-items-end flex-wrap-reverse flex-sm-nowrap">
          <CustomInput
            id="show-global"
            type="checkbox"
            className="d-flex mb-3"
            checked={sourceListsState.onlyGlobal}
            onChange={this.onGlobalOnlyClick}
            label={t('sourceListsTab.showGlobalCheck')}
          />
          <Button
            color="primary"
            className="btn-icon mb-3"
            onClick={actions.toggleAddListPopup}
          >
            <i className="lnr lnr-plus-circle btn-icon-wrapper" />
            {t('sourceListsTab.addListBtn')}
          </Button>
        </div>

        <SourceListsTable tableState={sourceListsState} actions={actions} />

        {isAddListPopupVisible && (
          <SourceListsAddPopup
            toggleAddListPopup={actions.toggleAddListPopup}
            addSourceList={actions.addSourceList}
          />
        )}

        {isDeletePopupVisible && (
          <SourceListsDeletePopup
            listToEdit={listToEdit}
            toggleDeleteListPopup={actions.toggleDeleteListPopup}
            deleteSourceList={actions.deleteSourceList}
          />
        )}

        {isRenameListPopupVisible && (
          <SourceListsRenamePopup
            listToEdit={listToEdit}
            toggleRenameListPopup={actions.toggleRenameListPopup}
            renameSourceList={actions.renameSourceList}
          />
        )}

        {isCloneListPopupVisible && (
          <SourceListsClonePopup
            listToEdit={listToEdit}
            toggleCloneListPopup={actions.toggleCloneListPopup}
            cloneSourceList={actions.cloneSourceList}
          />
        )}
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SourceLists)
