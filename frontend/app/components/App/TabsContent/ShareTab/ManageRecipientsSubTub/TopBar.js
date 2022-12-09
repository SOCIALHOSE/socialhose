import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import TableFilter from './TableFilter'
import TableSwitcher from '../common/TableSwitcher/TableSwitcher'
import { Button } from 'reactstrap'

export class TopBar extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tables: PropTypes.array.isRequired,
    tableVisible: PropTypes.string.isRequired,
    actions: PropTypes.object.isRequired
  };

  onNewRecipient = () => {
    const { actions } = this.props
    actions.startCreateRecipient()
  };

  onNewGroup = () => {
    const { actions } = this.props
    actions.startCreateGroup()
  };

  onFilterRequest = (filter) => {
    this.loadTable({ filter })
  };

  loadTable = (params) => {
    const { tableVisible: type } = this.props
    this.props.actions.shareTables[type].loadTable(params || null)
  };

  render () {
    const { t, tables, tableVisible, actions } = this.props

    return (
      <Fragment>
        <div className="notifications-topbar align-items-center">
          <TableSwitcher
            tables={tables}
            tableVisible={tableVisible}
            subTab="recipients"
            switchTable={actions.switchShareTable}
            loadTable={this.loadTable}
          />

          <div className="notifications-buttons">
            <Button 
              color="primary"
              className="btn-icon mr-2"
              onClick={this.onNewRecipient}
            >
              <i className="lnr lnr-location for-small btn-icon-wrapper" />
              {t('manageRecipientsTab.newRecipient')}
            </Button>
            <Button
              color="primary"
              className="btn-icon"
              onClick={this.onNewGroup}
            >
              <i className="lnr lnr-users for-small btn-icon-wrapper" />
              {t('manageRecipientsTab.newGroup')}
            </Button>
          </div>
        </div>
        <TableFilter
          type={tableVisible}
          onFilterRequest={this.onFilterRequest}
        />
      </Fragment>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(TopBar)
