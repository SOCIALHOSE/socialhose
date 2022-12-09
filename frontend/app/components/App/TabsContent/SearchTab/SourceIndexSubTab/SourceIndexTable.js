import React from 'react'
import PropTypes from 'prop-types'
import { translate, Interpolate } from 'react-i18next'

import Table from '../../../../common/Table/Table'
import CheckboxCell from '../../../../common/Table/CheckboxCell'
import SortableTh from '../../../../common/Table/SortableTh'
import SourceIndexInfoPopup from './SourceIndexInfoPopup'
import { Button } from 'reactstrap'
import { getTitle } from '../../../../../common/helper'

export class SourceIndexTable extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    type: PropTypes.string.isRequired,
    onFetch: PropTypes.func.isRequired,
    onDeleteIndex: PropTypes.func,
    actions: PropTypes.object.isRequired
  };

  onFetch = (page, pageSize, sorted) => {
    const { tableState, onFetch } = this.props
    const params = {
      page: page + 1,
      limit: pageSize,
      query: tableState.searchQuery
    }
    if (sorted.length) {
      const sortedField = sorted[0]
      const sort = {
        field: sortedField.id,
        direction: sortedField.desc ? 'desc' : 'asc'
      }
      params['sort'] = sort
    }
    onFetch(params)
  };

  selectAllAction = (event) => {
    const { actions } = this.props
    actions.toggleAllSourceIndexes()
  };

  selectRowAction = (itemId) => {
    const { actions } = this.props
    actions.toggleSourceIndex(itemId) // TODO
  };

  showUpdateSourcePopup = (source) => (e) => {
    e.preventDefault()
    this.props.actions.showUpdateSourcePopup(source)
  };

  deleteSourceIndex = (source) => (e) => {
    e.preventDefault()
    this.props.onDeleteIndex(source)
  };

  toggleInfoPopup = (source) => () => {
    const { type, actions } = this.props
    actions.toggleInfoSourcePopup(type, source)
  };

  getColumns = () => {
    const {t, type, tableState} = this.props

    let columns = [
      {
        id: 'selectCheckbox',
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
        Cell: ({original}) => {
          const isSelected = tableState.selectedIds.includes(original.id)
          return (
            <CheckboxCell
              id={original.id}
              checked={isSelected}
              onChange={this.selectRowAction}
            />
          )
        }
      }, {
        Header: <SortableTh title='sourceIndexTab.name' />,
        accessor: 'name',
        Cell: ({original}) => {
          return (
            <Button
              color="link"
              className="btn-anchor"
              title="Click to see details"
              onClick={this.toggleInfoPopup(original)}
            >
              {getTitle(original.title)}
            </Button>
          )
        }
      }, {
        id: 'mediaType',
        Header: <SortableTh title='sourceIndexTab.mediaType' />,
        accessor: item => t(`searchTab.sourceTypes.${item.type}`)
      }, {
        id: 'country',
        Header: <SortableTh title='sourceIndexTab.country' />,
        accessor: item => {
          return item.country ? t(`common:country.${item.country}`) : ''
        }
      }, {
        id: 'action',
        Header: t('sourceIndexTab.action'),
        sortable: false,
        Cell: ({original}) => {
          return (
            <Button
              outline
              color="info"
              className="border-0"
              size="sm"
              onClick={this.showUpdateSourcePopup(original)}
            >
              <Interpolate
                i18nKey='sourceIndexTab.actionBtn'
                listsCount={original.listIds.length}
              />
            </Button>
          )
        }
      }, {
        id: 'deleteAction',
        Header: t('sourceIndexTab.action'),
        sortable: false,
        Cell: ({original}) => {
          return (
            <Button
              outline
              size="sm"
              color="secondary"
              className="border-0"
              onClick={this.deleteSourceIndex(original)}
            >
              {t('sourceListsTab.delete')}
            </Button>
          )
        }
      }
    ]

    const sourceIndexCols = ['selectCheckbox', 'name', 'mediaType', 'country', 'action']
    const sourceOfListCols = ['name', 'mediaType', 'country', 'deleteAction']
    let cols = type === 'sourceIndexesState' ? sourceIndexCols : sourceOfListCols
    return columns.filter(col => cols.includes(col.id) || cols.includes(col.accessor))
  };

  render () {
    const {tableState} = this.props
    const columns = this.getColumns()
    const infoPopup = tableState.infoPopup

    return (
      <div className="sources-table">
        <Table
          columns={columns}
          data={tableState.data}
          totalCount={tableState.totalCount}
          showTotalCount
          limit={tableState.limit}
          page={tableState.page}
          isLoading={tableState.isLoading}
          onFetchData={this.onFetch}
        />

        {infoPopup.visible && infoPopup.item &&
          <SourceIndexInfoPopup
            source={infoPopup.item}
            hideSourceInfoPopup={this.toggleInfoPopup(null)}
          />
        }

      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SourceIndexTable)
