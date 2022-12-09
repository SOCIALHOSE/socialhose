import React from 'react'
import PropTypes from 'prop-types'

import Table from '../../../../common/Table/Table'
import LinkCell from '../../../../common/Table/LinkCell'
import CheckboxCell from '../../../../common/Table/CheckboxCell'
import SortableTh from '../../../../common/Table/SortableTh'
import DeletePopup from './DeletePopup'
import Toggler from '../../../../common/Table/Toggler'
import { addOrdinalSuffix, padLeft } from '../../../../../common/StringUtils'
import DeleteButton from '../../../../common/Table/DeleteButton'
import Restrictions from '../../../../common/Restrictions/Restrictions'

export class GenericTable extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    type: PropTypes.string.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    tableActions: PropTypes.object.isRequired,
    deleteSingleText: PropTypes.string.isRequired,
    deleteMultipleText: PropTypes.string.isRequired
  };

  onDeleteButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.confirmDelete(tableState.selectedIds)
  };

  fetchData = (page, pageSize, sorted) => {
    const { tableActions } = this.props
    const params = {
      page: page + 1,
      limit: pageSize
    }
    if (sorted.length) {
      const sortedField = sorted[0]
      params['sortField'] = sortedField.id
      params['sortDirection'] = sortedField.desc ? 'desc' : 'asc'
    }
    tableActions.loadTable(params)
  };

  selectAllAction = () => {
    const { tableActions } = this.props
    tableActions.selectTableAllRows()
  };

  selectRowAction = (itemId) => {
    const { tableActions } = this.props
    tableActions.selectTableRow(itemId)
  };

  deleteRowAction = (itemId) => {
    const { tableActions } = this.props
    tableActions.confirmDelete([itemId])
  };

  nameClickAction = (item) => {
    //implement in subclasses
  };

  onActivateButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.toggleActive(tableState.selectedIds, true)
  };

  onPauseButtonClick = () => {
    const { tableState, tableActions } = this.props
    tableActions.toggleActive(tableState.selectedIds, false)
  };

  scheduleFormat (schedules) {
    const { t } = this.props
    if (!schedules) {
      return ''
    } else if (schedules.length === 0) {
      return 'n/a'
    } else if (schedules.length === 1) {
      const schedule = schedules[0]
      if (schedule.type === 'daily') {
        const time = t(`notificationsTab.form.time.${schedule.time}`)
        const days = t(`notificationsTab.form.days.${schedule.days}`)
        return `${days}, ${time}`
      } else if (schedule.type === 'weekly') {
        const period = t(`notificationsTab.form.period.${schedule.period}`)
        const day = t(`notificationsTab.form.day.${schedule.day}`)
        const hour = padLeft(schedule.hour.toString(), 2)
        const minute = padLeft(schedule.minute.toString(), 2)
        return `${period} ${day}, ${hour}:${minute}`
      } else if (schedule.type === 'monthly') {
        const monthDay = addOrdinalSuffix(schedule.day)
        const hour = padLeft(schedule.hour.toString(), 2)
        const minute = padLeft(schedule.minute.toString(), 2)
        return `${monthDay} of the month, ${hour}:${minute}`
      }
      return ''
    } else {
      return (
        schedules.length + ' ' + this.props.t('notificationsTab.scheduledTimes')
      )
    }
  }

  getRestrictions (restrictions) {
    if (!restrictions) return null
    return (
      <Restrictions restrictions={restrictions} restrictionsIds={['alerts', 'webFeeds']} />
    )
  }

  getActionsPanel () {
    //implement in subclasess
  }

  defineColumns () {
    const { t, tableState } = this.props

    return {
      selectCheckbox: {
        accessor: '',
        sortable: false,
        width: 45,
        className: 'cw-center-cell',
        headerClassName: 'cw-center-cell',
        Header: () => {
          return (
            <CheckboxCell
              checked={tableState.isAllSelected}
              onChange={this.selectAllAction}
            />
          )
        },
        Cell: ({ original }) => {
          const isSelected = tableState.selectedIds.includes(original.id)
          return (
            <CheckboxCell
              id={original.id}
              checked={isSelected}
              onChange={this.selectRowAction}
            />
          )
        }
      },

      name: {
        Header: <SortableTh title="notificationsTab.name" />,
        accessor: 'name',
        Cell: ({ original }) => {
          return (
            <LinkCell item={original} onClick={this.nameClickAction}>
              {original.name}
            </LinkCell>
          )
        }
      },

      type: {
        Header: <SortableTh title="notificationsTab.type" />,
        accessor: (item) => t(`notificationsTab.${item.type}`),
        width: 100
      },

      owner: {
        Header: <SortableTh title="notificationsTab.owner" />,
        accessor: (item) => item.owner.email,
        width: 170
      },

      ScheduledTimes: {
        sortable: false,
        Header: t('notificationsTab.ScheduledTimes'),
        accessor: (item) => this.scheduleFormat(item.automatic),
        width: 170
      },

      sourcesCount: {
        Header: <SortableTh title="notificationsTab.contents" />,
        accessor: (item) =>
          `${item.sourcesCount} ${t('notificationsTab.chartsFeeds')}`,
        width: 170
      },

      delete: {
        sortable: false,
        Header: t('common:commonWords.Delete'),
        accessor: '',
        width: 65,
        className: 'cw-center-cell',
        headerClassName: 'cw-center-cell',
        Cell: ({ original }) => {
          return (
            <DeleteButton id={original.id} onDelete={this.deleteRowAction} />
          )
        }
      }
    }
  }

  createTogglerColumn (
    title,
    toggleField,
    enabledText,
    disabledText,
    togglerOnAction,
    togglerOffAction
  ) {
    const { t } = this.props
    return {
      Header: t(title),
      sortable: false,
      accessor: toggleField,
      width: 180,
      Cell: ({ original }) => {
        return (
          <Toggler
            id={original.id}
            turnOnAction={togglerOnAction}
            turnOffAction={togglerOffAction}
            state={original[toggleField]}
            enabledText={enabledText}
            disabledText={disabledText}
          />
        )
      }
    }
  }

  getColumns () {
    //implement in subclasses
    //should return array of string
  }

  _fixColDefs (colDefs) {
    for (let colId in colDefs) {
      let colDef = colDefs[colId]
      if (typeof colDef.accessor !== 'string' && !colDef.id) {
        colDef.id = colId
      }
    }
  }

  noCard () {
    // inherited class will return value
  }

  render () {
    const {
      tableActions,
      tableState,
      deleteSingleText,
      deleteMultipleText
    } = this.props

    const cols = this.getColumns()
    const colDefinitions = this.defineColumns()
    const noCard = this.noCard()
    this._fixColDefs(colDefinitions)
    const columns = cols
      .map((columnId) => {
        const col = colDefinitions[columnId]
        if (!col) {
          console.error(this.displayName, ': cannot find column', columnId)
        }
        return col
      })
      .filter(Boolean)

    return (
      <div>
        {this.getActionsPanel()}
        <div>
          <Table
            columns={columns}
            data={tableState.data}
            totalCount={tableState.totalCount}
            limit={tableState.limit}
            page={tableState.page}
            isLoading={tableState.isLoading}
            onFetchData={this.fetchData}
            onRowClick={this.onRowClick}
            noCard={noCard}
          />

          {tableState.isDeletePopupVisible && (
            <DeletePopup
              actions={tableActions}
              idsToDelete={tableState.idsToDelete}
              deleteSingleText={deleteSingleText}
              deleteMultipleText={deleteMultipleText}
            />
          )}
        </div>
      </div>
    )
  }
}

export default GenericTable
