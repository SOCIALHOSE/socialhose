import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { NavItem, NavLink } from 'reactstrap'

export class TablesTabs extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tabs: PropTypes.array.isRequired,
    activeTab: PropTypes.string.isRequired,
    chooseTableTab: PropTypes.func.isRequired
  }

  chooseTableTab = (tab) => () => {
    this.props.chooseTableTab(tab)
  }

  render() {
    const { t, tabs, activeTab } = this.props

    return tabs.map((tab, i) => (
      <NavItem key={tab}>
        <NavLink
          key={`table-tab-${i}`}
          active={tab === activeTab}
          onClick={this.chooseTableTab(tab)}
        >
          {t(`manageRecipientsTab.tables.${tab}`)}
        </NavLink>
      </NavItem>
    ))
  }
}

export default translate(['tabsContent'], { wait: true })(TablesTabs)
