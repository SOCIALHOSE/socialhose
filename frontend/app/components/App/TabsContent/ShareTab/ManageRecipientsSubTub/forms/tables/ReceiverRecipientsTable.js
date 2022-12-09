import React from 'react'
import PropTypes from 'prop-types'
import {translate} from 'react-i18next'
import ReceiverFormTable from './ReceiverFormTable'
import SortableTh from '../../../../../../common/Table/SortableTh'
import FormTableTopBar from './FormTableTopBar'
import { convertUTCtoLocal } from '../../../../../../../common/helper'

export class ReceiverRecipientsTable extends ReceiverFormTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    receiver: PropTypes.object.isRequired,
    formActions: PropTypes.object.isRequired
  };

  togglerOnAction = (itemId) => {
    this.props.formActions.toggleRecipient(itemId, true)
    this.props.tableActions.toggleEnrolled(itemId, true)
  };

  togglerOffAction = (itemId) => {
    this.props.formActions.toggleRecipient(itemId, false)
    this.props.tableActions.toggleEnrolled(itemId, false)
  };

  defineColumns () {
    const {t} = this.props
    return {
      ...super.defineColumns(),
      'enrolled': this.createTogglerColumn('manageRecipientsTab.form.recipient.enroll', 'enrolled', 'yes', 'no', this.togglerOnAction, this.togglerOffAction),
      'name': {
        Header: <SortableTh title='manageRecipientsTab.name' />,
        accessor: item => `${item.firstName} ${item.lastName}`
      },
      'email': {
        Header: <SortableTh title='manageRecipientsTab.email' />,
        accessor: 'email',
        width: 170
      },
      'addedDate': {
        Header: t('manageRecipientsTab.form.group.addedDate'),
        accessor: item => item.creationDate ? convertUTCtoLocal(item.creationDate, 'DD MMM YYYY HH:mm') : '',
        width: 170
      }
    }
  }

  getColumns () {
    return ['name', 'email', 'addedDate', 'active', 'enrolled']
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
        type="recipients"
        yesText="Enrolled"
        noText="NotEnrolled"
        allText="All"
      />
    )
  }
}

export default translate(['tabsContent'], { wait: true })(ReceiverRecipientsTable)
