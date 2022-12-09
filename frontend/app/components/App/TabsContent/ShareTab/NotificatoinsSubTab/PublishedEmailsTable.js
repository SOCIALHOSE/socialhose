import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import GenericTable from '../common/GenericTable'
import {NOTIFICATION_TABLES} from '../../../../../redux/modules/appState/share/tabs'
import SortableTh from '../../../../common/Table/SortableTh'
import { Button, ButtonGroup } from 'reactstrap'

class PublishedEmailsTable extends GenericTable {

  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    restrictions: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  };

  onSubscribeButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.toggleSubscribe(tableState.selectedIds, true)
  };

  onUnSubscribeButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.toggleSubscribe(tableState.selectedIds, false)
  };

  togglerOnAction = (itemId) => {
    this.props.tableActions.toggleSubscribe([itemId], true)
  };

  togglerOffAction = (itemId) => {
    const {tableState, tableActions, actions} = this.props
    const notification = tableState.data.find(item => item.id === itemId)
    if (notification.allowUnsubscribe) {
      tableActions.toggleSubscribe([itemId], false)
    } else {
      actions.addAlert({type: 'error', transKey: 'cannotUnsubscribe'})
    }
  };

  nameClickAction = (item) => {
    const { actions } = this.props
    actions.startEditNotification(item, NOTIFICATION_TABLES.PUBLISHED)
  };

  defineColumns () {
    const {t} = this.props
    const colDefinitions = super.defineColumns()
    return {
      ...colDefinitions,
      'subscribed': this.createTogglerColumn('notificationsTab.action', 'subscribed', 'subscribed', 'unsubscribed', this.togglerOnAction, this.togglerOffAction),
      'active': {
        Header: <SortableTh title='notificationsTab.status' />,
        accessor: item => item.active ? t('notificationsTab.active') : t('notificationsTab.paused'),
        width: 100
      }
    }
  };

  getColumns () {
    return ['selectCheckbox', 'name', 'type', 'owner', 'ScheduledTimes', 'active', 'subscribed']
  }

  getActionsPanel () {
    const {t, restrictions} = this.props

    return (
      <Fragment>
        {this.getRestrictions(restrictions)}
        
        <ButtonGroup className="mb-3">
          <Button onClick={this.onSubscribeButtonClick}>
            <i className="fa fa-envelope for-small mr-1" /> {t('notificationsTab.subscribe')}
          </Button>
          <Button onClick={this.onUnSubscribeButtonClick}>
            <i className="fa fa-times for-small mr-1" /> {t('notificationsTab.unsubscribe')}
          </Button>
        </ButtonGroup>
      </Fragment>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(PublishedEmailsTable)
