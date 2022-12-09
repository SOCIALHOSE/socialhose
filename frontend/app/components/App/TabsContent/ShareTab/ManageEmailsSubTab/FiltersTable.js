import React from 'react'
import PropTypes from 'prop-types'
import {translate} from 'react-i18next'
import {GenericTable} from '../common/GenericTable'
import SortableTh from '../../../../common/Table/SortableTh'
import {EMAILS_SUBSCREENS} from '../../../../../redux/modules/appState/share/tabs'

export class FiltersTable extends GenericTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired
  };

  defineColumns () {

    return {
      'name': {
        Header: <SortableTh title='manageEmailsTab.filter' />,
        accessor: 'name'
      },
      'notifications': {
        Header: <SortableTh title='manageEmailsTab.notifications' />,
        width: 270,
        accessor: 'notifications'
      }
    }
  }

  getColumns () {
    return ['name', 'notifications']
  }

  onRowClick = (e, state, rowInfo) => {
    const { actions } = this.props
    const filter = {
      type: rowInfo.original.type,
      id: rowInfo.original.id,
      name: rowInfo.original.name
    }
    actions.shareTables.emails.setFilter(filter)
    actions.switchShareSubScreen('emails', EMAILS_SUBSCREENS.EMAILS_TABLE)
    actions.shareTables.emails.loadTable({})
  };

}

export default translate(['tabsContent'], { wait: true })(FiltersTable)
