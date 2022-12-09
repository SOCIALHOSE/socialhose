import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Button } from 'reactstrap'

export class TableSwitcherItem extends React.PureComponent {
  static propTypes = {
    table: PropTypes.string.isRequired,
    tableVisible: PropTypes.string.isRequired,
    onClick: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  onClick = () => {
    const { onClick, table } = this.props
    onClick(table)
  };

  render () {
    const { t, table, tableVisible } = this.props

    return (
      <Button
        outline
        color="info"
        onClick={this.onClick}
        active={tableVisible === table}
      >
        {t(`tableSwitcher.${table}`)}
      </Button>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(TableSwitcherItem)
