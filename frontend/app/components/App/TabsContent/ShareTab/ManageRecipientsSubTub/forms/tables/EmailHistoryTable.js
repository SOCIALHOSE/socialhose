import React from 'react'
import PropTypes from 'prop-types'
import {translate} from 'react-i18next'
import ReceiverFormTable from './ReceiverFormTable'
import SortableTh from '../../../../../../common/Table/SortableTh'
import { convertUTCtoLocal } from '../../../../../../../common/helper'

export class EmailHistoryTable extends ReceiverFormTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    receiver: PropTypes.object.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired
  };

  defineColumns () {
    const {t} = this.props
    return {
      ...super.defineColumns(),
      'ScheduledTimes': {
        sortable: false,
        Header: t('notificationsTab.ScheduledTimes'),
        accessor: item => this.scheduleFormat(item.schedule),
        width: 170
      },

      'sentTime': {
        Header: <SortableTh title='notificationsTab.sentTime' />,
        accessor: item => convertUTCtoLocal(item.sentTime, 'DD MMM YYYY HH:mm'),
        width: 170
      }
    }
  }

  getColumns () {
    return ['name', 'type', 'ScheduledTimes', 'sentTime']
  }

  noCard () {
    return true
  }
}

export default translate(['tabsContent'], { wait: true })(EmailHistoryTable)
