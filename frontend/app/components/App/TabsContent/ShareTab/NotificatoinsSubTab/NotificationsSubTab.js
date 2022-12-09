import React from 'react'
import PropTypes from 'prop-types'
import TopBar from './TopBar'
import Navigation from './Navigation'
import AlertForm from './forms/AlertForm'
import {NOTIFICATION_TABLES, NOTIFICATION_SUBSCREENS} from '../../../../../redux/modules/appState/share/tabs'
import MyEmailsTable from './MyEmailsTable'
import PublishedEmailsTable from './PublishedEmailsTable'
import {withRouter} from 'react-router-dom'
import reduxConnect from '../../../../../redux/utils/connect'
import {compose} from 'redux'
import { setDocumentData } from '../../../../../common/helper'

class NotificationsSubTab extends React.Component {
  static propTypes = {
    store: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

  _shareState = () => this.props.store.appState.share;

  _authState = () => this.props.store.common.auth;

  componentDidMount() {
    setDocumentData('title', 'Alerts | Share')
  }

  componentWillUnmount() {
    setDocumentData('title')
  }

  render () {
    const { actions } = this.props

    const shareState = this._shareState()
    const {user: {restrictions}} = this._authState()

    const { subScreenVisible, tableVisible } = shareState.tabs.notifications

    return (
      <div className="notifications-tab">

        {subScreenVisible === NOTIFICATION_SUBSCREENS.TABLES &&
          <div>
            <TopBar
              tables={[NOTIFICATION_TABLES.MY_EMAILS, NOTIFICATION_TABLES.PUBLISHED]}
              tableVisible={tableVisible}
              actions={actions}
            />

            {tableVisible === NOTIFICATION_TABLES.MY_EMAILS &&
              <MyEmailsTable
                tableState={shareState.tables[tableVisible]}
                restrictions={restrictions && restrictions.limits}
                actions={actions}
                tableActions={actions.shareTables[tableVisible]}
                deleteSingleText='alert'
                deleteMultipleText='alerts'
              />
            }

            {tableVisible === NOTIFICATION_TABLES.PUBLISHED &&
              <PublishedEmailsTable
                tableState={shareState.tables[tableVisible]}
                restrictions={restrictions && restrictions.limits}
                actions={actions}
                tableActions={actions.shareTables[tableVisible]}
                deleteSingleText='alert'
                deleteMultipleText='alerts'
              />

            }
          </div>
        }

        {(subScreenVisible === NOTIFICATION_SUBSCREENS.ALERT_FORM || subScreenVisible === NOTIFICATION_SUBSCREENS.NEWSLETTER_FORM) &&
          <Navigation actions={actions} />
        }

        {subScreenVisible === NOTIFICATION_SUBSCREENS.ALERT_FORM &&
          <AlertForm
            state={shareState.forms.alert}
            switchShareSubScreen={actions.switchShareSubScreen}
            actions={actions.shareForms.alert}
          />
        }

        {/* {subScreenVisible === NOTIFICATION_SUBSCREENS.NEWSLETTER_FORM &&
          <NewsletterForm
            state={shareState.forms.newsletter}
            switchShareSubScreen={actions.switchShareSubScreen}
            actions={actions.shareForms.newsletter}
          />
        } */}

      </div>
    )
  }
}

const applyDecorators = compose(
  withRouter,
  reduxConnect()
)

export default applyDecorators(NotificationsSubTab)
