import React from 'react'
import PropTypes from 'prop-types'
import {translate} from 'react-i18next'
import ReceiverFormTable from './ReceiverFormTable'
import SortableTh from '../../../../../../common/Table/SortableTh'
import LinkCell from '../../../../../../common/Table/LinkCell'
import FormTableTopBar from './FormTableTopBar'

export class ReceiverGroupsTable extends ReceiverFormTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    receiver: PropTypes.object.isRequired,
    formActions: PropTypes.object.isRequired
  };

  togglerOnAction = (itemId) => {
    this.props.formActions.toggleGroup(itemId, true)
    this.props.tableActions.toggleEnrolled(itemId, true)
  };

  togglerOffAction = (itemId) => {
    this.props.formActions.toggleGroup(itemId, false)
    this.props.tableActions.toggleEnrolled(itemId, false)
  };

  _formatSubscriptions (subscriptions) {
    console.log('format subsc', subscriptions)
    const result = []
    if (subscriptions.alert > 0) {
      result.push(`${subscriptions.alert} Alerts`)
    }
    if (subscriptions.newsletter > 0) {
      result.push(`${subscriptions.newsletter} Newsletters`)
    }
    return result.join(', ')
  };

  _formatRecipients (number) {
    if (number) {
      if (number === 1) {
        return '1 Recipient'
      } else {
        return number + ' Recipients'
      }
    }
    return ''
  }

  defineColumns () {
    const {t} = this.props
    return {
      ...super.defineColumns(),
      'groupName': {
        Header: <SortableTh title='manageRecipientsTab.groupName' />,
        accessor: 'name',
        Cell: (row) => {
          return (
            <LinkCell item={row.original} onClick={this.nameClickAction}>
              {row.value}
            </LinkCell>
          )
        }
      },
      'enrolled': this.createTogglerColumn('manageRecipientsTab.form.recipient.enroll', 'enrolled', 'yes', 'no', this.togglerOnAction, this.togglerOffAction),
      'subscriptions': {
        sortable: false,
        Header: t('manageRecipientsTab.subscriptions'),
        accessor: item => this._formatSubscriptions(item.subscriptions),
        width: 170
      },
      'recipients': {
        sortable: false,
        Header: t('manageRecipientsTab.recipients'),
        accessor: item => this._formatRecipients(item.recipients.length),
        width: 170
      }
    }
  }

  getColumns () {
    return ['groupName', 'subscriptions', 'recipients', 'active', 'enrolled']
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
        type="groups"
        yesText="Enrolled"
        noText="NotEnrolled"
        allText="All"
      />
    )
  }

}

export default translate(['tabsContent'], { wait: true })(ReceiverGroupsTable)
