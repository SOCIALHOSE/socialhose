import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import Select from 'react-select'
import { EMAILS_SUBSCREENS } from '../../../../../redux/modules/appState/share/tabs'
import { Button } from 'reactstrap'

export class FiltersTopBar extends React.Component {
  static propTypes = {
    actions: PropTypes.object.isRequired,
    filterType: PropTypes.string.isRequired,
    t: PropTypes.func.isRequired
  };

  onSelectFilterType = (filterType) => {
    const { actions } = this.props
    actions.shareTables.emailFilters.loadTable({ filterType })
  };

  clearFilters = () => {
    const { actions } = this.props
    actions.shareTables.emails.clearFilter()
    actions.switchShareSubScreen('emails', EMAILS_SUBSCREENS.EMAILS_TABLE)
  };

  backToTable = () => {
    const { actions } = this.props
    actions.switchShareSubScreen('emails', EMAILS_SUBSCREENS.EMAILS_TABLE)
  }

  filterTypes = [
    { label: 'Owner', value: 'owner' },
    { label: 'Recipient', value: 'recipient' },
    { label: 'Feed', value: 'feed' }
  ];

  render () {
    const { t, filterType } = this.props

    return (
      <Fragment>
        <Button className="btn-wide mb-2" size="sm" color="info" onClick={this.backToTable}>
          <i className="lnr lnr-chevron-left"> </i>
        </Button>
        <div className="notifications-topbar align-items-center">
          <div className="text-muted">{t('manageEmailsTab.emailFilter')}</div>
          <div className="d-flex align-items-center">
            <label className="mr-1">{t('manageEmailsTab.filterBy')}</label>
            <div style={{ minWidth: '150px' }}>
              <Select
                value={filterType}
                onChange={this.onSelectFilterType}
                options={this.filterTypes}
                simpleValue
                searchable={false}
                clearable={false}
              />
            </div>

            <Button
              color="secondary"
              className="ml-2"
              onClick={this.clearFilters}
            >
              {t('manageEmailsTab.allEmails')}
            </Button>
          </div>
        </div>
      </Fragment>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(FiltersTopBar)
