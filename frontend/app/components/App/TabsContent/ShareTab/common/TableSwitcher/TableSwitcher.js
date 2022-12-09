import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import TableSwitcherItem from './TableSwitcherItem'
import { ButtonGroup } from 'reactstrap'

export class TableSwitcher extends React.Component {
  static propTypes = {
    tables: PropTypes.array.isRequired,
    tableVisible: PropTypes.string.isRequired,
    subTab: PropTypes.string.isRequired,
    switchTable: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  onTableClick = (item) => {
    const { subTab, switchTable } = this.props
    switchTable(subTab, item)
  };

  render () {
    const { tables, tableVisible } = this.props

    return (
      <ButtonGroup className="bg-white">
        {tables.map((table, i) => {
          return (
            <TableSwitcherItem
              key={`tables-switcher__table-${i}`}
              tableVisible={tableVisible}
              table={table}
              onClick={this.onTableClick}
            />
          )
        })}
      </ButtonGroup>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(TableSwitcher)
