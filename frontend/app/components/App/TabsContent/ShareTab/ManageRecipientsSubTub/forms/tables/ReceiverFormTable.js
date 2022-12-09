import React from 'react'
import PropTypes from 'prop-types'
import GenericTable from '../../../common/GenericTable'
import SortableTh from '../../../../../../common/Table/SortableTh'

class ReceiverFormTable extends GenericTable {

  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    receiver: PropTypes.object.isRequired
  };

  fetchData = (page, pageSize, sorted) => {
    const { tableActions, receiver } = this.props
    const params = {
      page: page + 1,
      limit: pageSize
    }
    if (sorted.length) {
      const sortedField = sorted[0]
      params['sortField'] = sortedField.id
      params['sortDirection'] = sortedField.desc ? 'desc' : 'asc'
    }
    tableActions.loadTable(params, receiver)
  };

  defineColumns () {
    const {t} = this.props
    const colDefs = super.defineColumns()
    return {
      ...colDefs,
      'active': {
        Header: <SortableTh title='notificationsTab.status' />,
        accessor: item => item.active ? t('notificationsTab.active') : t('notificationsTab.paused'),
        width: 100
      }
    }
  }

}

export default ReceiverFormTable
