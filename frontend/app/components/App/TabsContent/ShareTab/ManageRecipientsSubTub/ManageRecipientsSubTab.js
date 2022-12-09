import React from 'react'
import PropTypes from 'prop-types'
import TopBar from './TopBar'
import RecipientsTable from './RecipientsTable'
import { RECEIVER_TABLES, RECEIVER_SUBSCREENS } from '../../../../../redux/modules/appState/share/tabs'
import {RecipientForm} from './forms/ReceiverForm'
import GroupsTable from './GroupsTable'
import {withRouter} from 'react-router-dom'
import reduxConnect from '../../../../../redux/utils/connect'
import {compose} from 'redux'
import { setDocumentData } from '../../../../../common/helper'

class ManageRecipientsSubTab extends React.Component {
  static propTypes = {
    shareState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

  componentDidMount() {
    setDocumentData('title', 'Manage Emails | Share')
  }

  componentWillUnmount() {
    setDocumentData('title')
  }

  render () {

    const { shareState, actions } = this.props
    const { subScreenVisible, tableVisible } = shareState.tabs.recipients
    const tableState = shareState.tables[tableVisible]

    return (
      <div className="notifications-tab">

        {subScreenVisible === RECEIVER_SUBSCREENS.TABLES &&
          <div>
            <TopBar
              tableVisible={tableVisible}
              tables={[RECEIVER_TABLES.RECIPIENTS, RECEIVER_TABLES.GROUPS]}
              actions={actions}
            />

            {tableVisible === RECEIVER_TABLES.RECIPIENTS &&
              <RecipientsTable
                tableState={tableState}
                actions={actions}
                tableActions={actions.shareTables[tableVisible]}
                deleteSingleText='recipient'
                deleteMultipleText='recipients'
              />
            }
            {tableVisible === RECEIVER_TABLES.GROUPS &&
              <GroupsTable
                tableState={tableState}
                actions={actions}
                tableActions={actions.shareTables[tableVisible]}
                deleteSingleText='group'
                deleteMultipleText='groups'
              />
            }
          </div>
        }

        {(subScreenVisible === RECEIVER_SUBSCREENS.RECIPIENT_FORM || subScreenVisible === RECEIVER_SUBSCREENS.GROUP_FORM) &&
          <RecipientForm
            formType={subScreenVisible}
            shareState={shareState}
            actions={actions}
          />
        }

      </div>
    )
  }
}

const applyDecorators = compose(
  withRouter,
  reduxConnect('shareState', ['appState', 'share'])
)

export default applyDecorators(ManageRecipientsSubTab)
