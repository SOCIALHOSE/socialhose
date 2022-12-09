import React, { Fragment } from 'react'
import GenericTable from '../common/GenericTable'
import { translate } from 'react-i18next'
import PropTypes from 'prop-types'
import { NOTIFICATION_TABLES } from '../../../../../redux/modules/appState/share/tabs'
import SortableTh from '../../../../common/Table/SortableTh'
import { ButtonGroup, Button } from 'reactstrap'

export class MyEmailsTable extends GenericTable {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    restrictions: PropTypes.object,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  };

  togglerOnAction = (itemId) => {
    this.props.tableActions.toggleActive([itemId], true)
  };

  togglerOffAction = (itemId) => {
    this.props.tableActions.toggleActive([itemId], false)
  };

  nameClickAction = (item) => {
    const { actions } = this.props
    actions.startEditNotification(item, NOTIFICATION_TABLES.MY_EMAILS)
  };

  onPublishButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.togglePublish(tableState.selectedIds, true)
  };

  onUnPublishButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.togglePublish(tableState.selectedIds, false)
  };

  _recipientsFormat (recipients) {
    if (recipients.length === 1) {
      return recipients[0].email
    }
    return `${recipients.length} ${this.props.t(
      'notificationsTab.recipients'
    )}`
  }

  defineColumns () {
    const { t } = this.props

    const colDefinitions = super.defineColumns()
    return {
      ...colDefinitions,
      active: this.createTogglerColumn(
        'notificationsTab.action',
        'active',
        'active',
        'paused',
        this.togglerOnAction,
        this.togglerOffAction
      ),
      Recipients: {
        sortable: false,
        Header: t('notificationsTab.Recipients'),
        accessor: (item) => this._recipientsFormat(item.recipients),
        width: 110
      },
      published: {
        Header: <SortableTh title="notificationsTab.published" />,
        accessor: (item) =>
          item.published
            ? t('common:commonWords.Yes')
            : t('common:commonWords.No'),
        width: 100
      }
    }
  }

  getColumns () {
    return [
      'selectCheckbox',
      'name',
      'type',
      'published',
      'ScheduledTimes',
      'sourcesCount',
      'Recipients',
      'active',
      'delete'
    ]
  }

  getActionsPanel () {
    const { t, restrictions } = this.props
    return (
      <Fragment>
        {this.getRestrictions(restrictions)}

        <ButtonGroup className="mb-3">
          <Button
            color="secondary"
            onClick={this.onActivateButtonClick}
          >
            <i className="fa fa-play for-small mr-1"> </i>{" "}
            {t('notificationsTab.activate')}
          </Button>

          <Button
            color="secondary"
            onClick={this.onPauseButtonClick}
          >
            <i className="fa fa-pause for-small mr-1"> </i>{" "}
            {t('notificationsTab.pause')}
          </Button>
          <Button
            color="secondary"
            onClick={this.onDeleteButtonClick}
          >
            <i className="fa fa-trash for-small mr-1"> </i>{" "}
            {t('notificationsTab.delete')}
          </Button>
          <Button
            color="secondary"
            onClick={this.onPublishButtonClick}
          >
            <i className="fa fa-upload for-small mr-1"> </i>{" "}
            {t('notificationsTab.publish')}
          </Button>
          <Button
            color="secondary"
            onClick={this.onUnPublishButtonClick}
          >
            <i className="fa fa-ban for-small mr-1"> </i>{" "}
            {t('notificationsTab.unpublish')}
          </Button>
        </ButtonGroup>
      </Fragment>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(MyEmailsTable)
