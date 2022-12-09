import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import ReceiversTable from './ReceiversTable'
import SortableTh from '../../../../common/Table/SortableTh'
import LinkCell from '../../../../common/Table/LinkCell'

class RecipientsTable extends ReceiversTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  };

  nameClickAction = (item) => {
    this.props.actions.startEditRecipient(item)
  };

  defineColumns () {
    const {t} = this.props
    const colDefs = super.defineColumns()
    return {
      ...colDefs,
      'email': {
        Header: <SortableTh title='manageRecipientsTab.email' />,
        accessor: 'email',
        width: 170
      },
      'groups': {
        sortable: false,
        Header: t('manageRecipientsTab.groups'),
        accessor: item => item.groups.map(group => group.name).join(', '),
        width: 170
      },
      'name': {
        Header: <SortableTh title='manageRecipientsTab.name' />,
        accessor: 'name',
        Cell: (row) => {
          const {original} = row
          const name = `${original.firstName} ${original.lastName}`
          return (
            <LinkCell item={original} onClick={this.nameClickAction}>
              {name}
            </LinkCell>
          )
        }
      }
    }
  }

  getColumns () {
    return ['selectCheckbox', 'name', 'email', 'groups', 'subscriptions', 'creationDate', 'active']
  }

}

export default translate(['tabsContent'], { wait: true })(RecipientsTable)
