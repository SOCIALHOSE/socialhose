import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { EMAILS_SUBSCREENS } from '../../../../../redux/modules/appState/share/tabs'
import { Button } from 'reactstrap'

export class TopBar extends React.Component {
  static propTypes = {
    actions: PropTypes.object.isRequired,
    tableState: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  onCreate = (type) => () => {
    const { actions } = this.props
    actions.startCreateNotification(type, 'emails', 'emails')
  };

  goToFiltersTable = () => {
    const { actions } = this.props
    actions.switchShareSubScreen('emails', EMAILS_SUBSCREENS.FILTERS_TABLE)
  };

  render () {
    const {
      t,
      tableState: { filter }
    } = this.props

    const filterName = filter
      ? `${filter.name} (${t('manageEmailsTab.' + filter.type)})`
      : t('manageEmailsTab.allEmails')

    return (
      <div className="notifications-topbar">
        <p className="text-muted align-self-center">
          <strong>{t('manageEmailsTab.currentFilter') + ': '}</strong>{" "}
          {filterName}
        </p>
        <div>
          <Button 
            className="btn-icon mr-2" 
            onClick={this.goToFiltersTable}
          >
            <i className="lnr lnr-funnel btn-icon-wrapper" />
            {t('manageEmailsTab.selectFilter')}
          </Button>
          <div className="notifications-buttons">
            <Button
              color="primary"
              className="btn-icon"
              onClick={this.onCreate(EMAILS_SUBSCREENS.ALERT_FORM)}
            >
              <i className="lnr lnr-alarm btn-icon-wrapper" />
              {t('notificationsTab.newAlert')}
            </Button>
            {/* <Button
              color="primary"
              className="btn-icon"
              onClick={this.onCreate(EMAILS_SUBSCREENS.NEWSLETTER_FORM)}
            >
              <i className="lnr lnr-file-add btn-icon-wrapper" />
              {t('notificationsTab.newNewsletter')}
            </Button> */}
          </div>
        </div>
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(TopBar)
