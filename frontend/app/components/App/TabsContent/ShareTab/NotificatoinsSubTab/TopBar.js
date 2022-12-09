import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import TableSwitcher from '../common/TableSwitcher/TableSwitcher'
import { NOTIFICATION_SUBSCREENS } from '../../../../../redux/modules/appState/share/tabs'
import { Button } from 'reactstrap'
class TopBar extends React.Component {
  static propTypes = {
    actions: PropTypes.object.isRequired,
    tables: PropTypes.array.isRequired,
    tableVisible: PropTypes.string.isRequired,
    t: PropTypes.func.isRequired
  }

  onCreate = (type) => () => {
    const { actions, tableVisible } = this.props
    actions.startCreateNotification(type, tableVisible)
  }

  loadTable = (type) => {
    this.props.actions.shareTables[type].loadTable(null)
  }

  render() {
    const { t, tables, tableVisible, actions } = this.props

    return (
      <div className="notifications-topbar">
        <div className="notifications-topbar_buttons_wrap">
          <TableSwitcher
            tables={tables}
            tableVisible={tableVisible}
            subTab="notifications"
            switchTable={actions.switchShareTable}
            loadTable={this.loadTable}
          />

          <div className="notifications-buttons">
            <Button className="btn-icon" color="primary" onClick={this.onCreate(NOTIFICATION_SUBSCREENS.ALERT_FORM)}>
              <i className="lnr lnr-alarm btn-icon-wrapper"></i>
              {t('notificationsTab.newAlert')}
            </Button>
          </div>
        </div>
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(TopBar)
