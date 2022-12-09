import React from 'react'
import PropTypes from 'prop-types'
import {translate} from 'react-i18next'
import ReceiverFormTable from './ReceiverFormTable'
import FormTableTopBar from './FormTableTopBar'

export class ReceiverSubscriptionsTable extends ReceiverFormTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    receiver: PropTypes.object.isRequired,
    formActions: PropTypes.object.isRequired
  };

  togglerOnAction = (itemId) => {
    this.props.formActions.toggleSubscription(itemId, true)
    this.props.tableActions.toggleSubscribed(itemId, true)
  };

  togglerOffAction = (itemId) => {
    this.props.formActions.toggleSubscription(itemId, false)
    this.props.tableActions.toggleSubscribed(itemId, false)
  };

  defineColumns () {
    return {
      ...super.defineColumns(),
      'subscribed': this.createTogglerColumn('notificationsTab.action', 'subscribed', 'subscribed', 'unsubscribed', this.togglerOnAction, this.togglerOffAction)
    }
  }

  getColumns () {
    return ['name', 'type', 'ScheduledTimes', 'active', 'subscribed']
  }

  noCard () {
    return true
  }

  getActionsPanel () {
    const {tableState, tableActions, receiver} = this.props
    return (
      <FormTableTopBar
        tableActions={tableActions}
        statusFilter={tableState.statusFilter}
        receiver={receiver}
        type="subscriptions"
        yesText="Subscribed"
        noText="Unsubscribed"
        allText="All"
      />
    )
  }
}

export default translate(['tabsContent'], { wait: true })(ReceiverSubscriptionsTable)
