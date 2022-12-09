import React from 'react'
import GenericTable from '../common/GenericTable'
import SortableTh from '../../../../common/Table/SortableTh'
import PropTypes from 'prop-types'
import { ButtonGroup, Button } from 'reactstrap'
import { convertUTCtoLocal } from '../../../../../common/helper'

class ReceiversTable extends GenericTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  };

  onActivateButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.toggleActive(tableState.selectedIds, true)
  };

  onPauseButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.toggleActive(tableState.selectedIds, false)
  };

  togglerOnAction = (itemId) => {
    const { tableActions } = this.props
    tableActions.toggleActive([itemId], true)
  };

  togglerOffAction = (itemId) => {
    const { tableActions } = this.props
    tableActions.toggleActive([itemId], false)
  };

  getActionsPanel = () => {
    const { t } = this.props

    return (
      <ButtonGroup className="mb-3">
        <Button
          color="secondary"
          onClick={this.onActivateButtonClick}
        >
          <i className="fa fa-play mr-1 for-small" />
          {t('notificationsTab.activate')}
        </Button>
        <Button
          color="secondary"
          onClick={this.onPauseButtonClick}
        >
          <i className="fa fa-pause for-small mr-1" />
          {t('notificationsTab.pause')}
        </Button>
        <Button
          color="secondary"
          onClick={this.onDeleteButtonClick}
        >
          <i className="fa fa-trash for-small mr-1" />
          {t('notificationsTab.delete')}
        </Button>
      </ButtonGroup>
    )
  };

  _formatSubscriptions (subscriptions) {
    const { t } = this.props
    const result = []
    if (subscriptions.alert > 0) {
      result.push(`${subscriptions.alert} ${t('notificationsTab.alerts')}`)
    }
    if (subscriptions.newsletter > 0) {
      result.push(`${subscriptions.newsletter} ${t('notificationsTab.newsletters')}`)
    }
    return result.join(', ')
  }

  defineColumns () {
    const { t } = this.props
    const colDefinitions = super.defineColumns()
    return {
      ...colDefinitions,
      subscriptions: {
        sortable: false,
        Header: t('manageRecipientsTab.subscriptions'),
        accessor: (item) => this._formatSubscriptions(item.subscriptions),
        width: 170
      },
      creationDate: {
        Header: <SortableTh title="manageRecipientsTab.creationDate" />,
        accessor: (item) => convertUTCtoLocal(item.creationDate, 'DD MMM YYYY HH:mm'),
        width: 100
      },
      active: this.createTogglerColumn(
        'manageRecipientsTab.status',
        'active',
        'active',
        'paused',
        this.togglerOnAction,
        this.togglerOffAction
      )
    }
  }
}

export default ReceiversTable
