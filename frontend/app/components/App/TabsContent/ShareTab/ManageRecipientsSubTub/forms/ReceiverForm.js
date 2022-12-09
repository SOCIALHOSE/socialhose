import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import FormTopBar from './FormTopBar'
import BasicRecipientInfo from './BasicRecipientInfo'
import BasicGroupInfo from './BasicGroupInfo'
import TablesTabs from './TablesTabs'
import EmailHistoryTable from './tables/EmailHistoryTable'
import DeletePopup from '../../common/DeletePopup'
import {
  RECIPIENT_FORM_TABLES,
  GROUP_FORM_TABLES,
  RECEIVER_SUBSCREENS
} from '../../../../../../redux/modules/appState/share/tabs'
import ReceiverSubscriptionsTable from './tables/ReceiverSubscriptionsTable'
import ReceiverGroupsTable from './tables/ReceiverGroupsTable'
import ReceiverRecipientsTable from './tables/ReceiverRecipientsTable'
import { Card, CardBody, CardHeader, Col, Nav, Row } from 'reactstrap'

export class RecipientForm extends React.Component {
  static propTypes = {
    formType: PropTypes.string.isRequired,
    shareState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  }

  chooseTableTab = (tab) => {
    const { actions, formType } = this.props
    actions.shareForms[formType].chooseTableTab(tab)
  }

  render() {
    const { formType, shareState, actions } = this.props
    const formState = shareState.forms[formType] // receiver
    const formActions = actions.shareForms[formType]

    let allTabs = formState.tabs.all
    if (!formState.id) {
      allTabs = allTabs.filter(
        (tab) => tab !== RECIPIENT_FORM_TABLES.EMAIL_HISTORY
      )
    }
    const activeTab = formState.tabs.active

    const tableState = shareState.tables.receiverForm[activeTab]
    const tableActions = actions.shareTables.receiverForm[activeTab]

    const deleteText =
      formType === RECEIVER_SUBSCREENS.GROUP_FORM ? 'group' : 'recipient'

    return (
      <Fragment>
        <FormTopBar
          formType={formType}
          receiver={formState}
          actions={actions}
        />

        <Row>
          <Col lg="4">
            {formType === RECEIVER_SUBSCREENS.RECIPIENT_FORM && (
              <BasicRecipientInfo item={formState} formActions={formActions} />
            )}

            {formType === RECEIVER_SUBSCREENS.GROUP_FORM && (
              <BasicGroupInfo item={formState} formActions={formActions} />
            )}
          </Col>

          <Col lg="8">
            <Card className="mb-3">
              <CardHeader>
                <Nav justified>
                  <TablesTabs
                    tabs={allTabs}
                    activeTab={activeTab}
                    chooseTableTab={this.chooseTableTab}
                  />
                </Nav>
              </CardHeader>
              <CardBody>
                {activeTab === RECIPIENT_FORM_TABLES.SUBSCRIPTIONS && (
                  <ReceiverSubscriptionsTable
                    tableState={tableState}
                    actions={actions}
                    tableActions={tableActions}
                    receiver={formState}
                    formActions={formActions}
                  />
                )}

                {activeTab === RECIPIENT_FORM_TABLES.GROUPS && (
                  <ReceiverGroupsTable
                    tableState={tableState}
                    actions={actions}
                    tableActions={tableActions}
                    receiver={formState}
                    formActions={formActions}
                  />
                )}

                {activeTab === RECIPIENT_FORM_TABLES.EMAIL_HISTORY && (
                  <EmailHistoryTable
                    type={activeTab}
                    tableState={tableState}
                    actions={actions}
                    tableActions={tableActions}
                    receiver={formState}
                  />
                )}

                {activeTab === GROUP_FORM_TABLES.RECIPIENTS && (
                  <ReceiverRecipientsTable
                    tableState={tableState}
                    actions={actions}
                    tableActions={tableActions}
                    receiver={formState}
                    formActions={formActions}
                  />
                )}
              </CardBody>
            </Card>
          </Col>
        </Row>

        {formState.isDeletePopupVisible && (
          <DeletePopup
            actions={formActions}
            idsToDelete={[formState.id]}
            deleteSingleText={deleteText}
          />
        )}
      </Fragment>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(RecipientForm)
