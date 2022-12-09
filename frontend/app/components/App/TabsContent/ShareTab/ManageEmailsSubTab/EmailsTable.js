import React from 'react'
import { translate } from 'react-i18next'
import PropTypes from 'prop-types'
import SortableTh from '../../../../common/Table/SortableTh'
import { MyEmailsTable } from '../NotificatoinsSubTab/MyEmailsTable' // default export doesn't work
import { ButtonGroup, Button } from 'reactstrap'

class EmailsTable extends MyEmailsTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  };

  nameClickAction = (item) => {
    const { actions } = this.props
    actions.startEditNotification(item, 'emails', 'emails')
  };

  defineColumns () {
    return {
      ...super.defineColumns(),
      owner: {
        Header: <SortableTh title="manageEmailsTab.owner" />,
        accessor: (item) => item.owner.email,
        width: 170
      }
    }
  }

  onRefreshButtonClick = () => {
    this.props.tableActions.loadTable({})
  };

  getColumns () {
    return [
      'selectCheckbox',
      'name',
      'type',
      'owner',
      'published',
      'ScheduledTimes',
      'sourcesCount',
      'Recipients',
      'active',
      'delete'
    ]
  }

  getActionsPanel = () => {
    const { t } = this.props
    return (
      <ButtonGroup className="mb-3">
        <Button
          onClick={this.onActivateButtonClick}
          color="secondary"
        >
          <i className="fa fa-play fa-1px for-small mr-1"> </i>{" "}
          {t('notificationsTab.activate')}
        </Button>

        <Button
          color="secondary"
          onClick={this.onPauseButtonClick}
        >
          <i className="fa fa-pause fa-1px for-small mr-1"> </i>{" "}
          {t('notificationsTab.pause')}
        </Button>
        <Button
          color="secondary"
          onClick={this.onDeleteButtonClick}
        >
          <i className="fa fa-trash for-small mr-1"> </i>{" "}
          {t('notificationsTab.delete')}
        </Button>
        <Button
          color="secondary"
          onClick={this.onRefreshButtonClick}
        >
          <i className="fa fa-refresh fa-1px for-small mr-1"> </i>{" "}
          {t('manageEmailsTab.refresh')}
        </Button>
      </ButtonGroup>
    )
  };
}

export default translate(['tabsContent'], { wait: true })(EmailsTable)
