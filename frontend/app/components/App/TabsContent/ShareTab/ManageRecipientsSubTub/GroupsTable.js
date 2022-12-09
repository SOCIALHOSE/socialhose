import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import ReceiversTable from './ReceiversTable'
import SortableTh from '../../../../common/Table/SortableTh'
import LinkCell from '../../../../common/Table/LinkCell'

class GroupsTable extends ReceiversTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  };

  nameClickAction = (item) => {
    this.props.actions.startEditGroup(item)
  };

  defineColumns () {
    //const {t} = this.props;
    const colDefs = super.defineColumns()
    return {
      ...colDefs,
      'recipientsNumber': {
        Header: <SortableTh title='manageRecipientsTab.recipientsNumber' />,
        accessor: item => item.recipients.length || '',
        width: 140
      },
      'name': {
        Header: <SortableTh title='manageRecipientsTab.groupName' />,
        accessor: 'name',
        Cell: (row) => {
          return (
            <LinkCell item={row.original} onClick={this.nameClickAction}>
              {row.value}
            </LinkCell>
          )
        }
      }
    }
  }

  getColumns () {
    return ['selectCheckbox', 'name', 'recipientsNumber', 'subscriptions', 'creationDate', 'active']
  }

}

export default translate(['tabsContent'], { wait: true })(GroupsTable)
