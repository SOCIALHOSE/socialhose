import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import TableFilter from '../../TableFilter'
import { Nav, NavItem, NavLink } from 'reactstrap'

class FormTableTopBar extends React.Component {
  static propTypes = {
    tableActions: PropTypes.object.isRequired,
    statusFilter: PropTypes.string.isRequired,
    type: PropTypes.string.isRequired,
    yesText: PropTypes.string.isRequired,
    noText: PropTypes.string.isRequired,
    allText: PropTypes.string.isRequired,
    receiver: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  }

  onFilterRequest = (filter) => {
    const { tableActions, receiver } = this.props
    tableActions.loadTable({ filter }, receiver)
  }

  onStatusFilter = (statusFilter) => {
    return () => {
      const { tableActions, receiver } = this.props
      tableActions.loadTable({ statusFilter }, receiver)
    }
  }

  render() {
    const {
      type,
      t,
      yesText,
      noText,
      allText,
      statusFilter,
      receiver
    } = this.props

    return (
      <div>
        {receiver.id && (
          <Nav pills justified>
            <NavItem>
              <NavLink
                className="d-block"
                active={statusFilter === 'all'}
                onClick={this.onStatusFilter('all')}
              >
                {t('manageRecipientsTab.' + allText)}
              </NavLink>
            </NavItem>
            <NavItem>
              <NavLink
                className="d-block"
                active={statusFilter === 'yes'}
                onClick={this.onStatusFilter('yes')}
              >
                {t('manageRecipientsTab.' + yesText)}
              </NavLink>
            </NavItem>
            <NavItem>
              <NavLink
                className="d-block"
                active={statusFilter === 'no'}
                onClick={this.onStatusFilter('no')}
              >
                {t('manageRecipientsTab.' + noText)}
              </NavLink>
            </NavItem>
          </Nav>
        )}

        <TableFilter type={type} onFilterRequest={this.onFilterRequest} />
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(FormTableTopBar)
