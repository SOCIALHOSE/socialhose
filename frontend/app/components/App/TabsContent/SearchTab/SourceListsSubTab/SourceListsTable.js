import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import moment from 'moment'

import Table from '../../../../common/Table/Table'
import SortableTh from '../../../../common/Table/SortableTh'
import { Button } from 'reactstrap'

export class SourceListsTable extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    tableState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  }

  onFetch = (page, pageSize, sorted) => {
    const { actions, tableState } = this.props
    const params = {
      page: page + 1,
      limit: pageSize,
      onlyShared: tableState.onlyGlobal
    }
    if (sorted.length) {
      const sortedField = sorted[0]
      const sort = {
        field: sortedField.id,
        direction: sortedField.desc ? 'desc' : 'asc'
      }
      params['sort'] = sort
    }
    actions.getMainSourceLists(params)
  }

  showDeleteListPopup = (item) => () => {
    this.props.actions.toggleDeleteListPopup(item)
  }

  showRenameListPopup = (item) => () => {
    this.props.actions.toggleRenameListPopup(item)
  }

  showCloneListPopup = (item) => () => {
    this.props.actions.toggleCloneListPopup(item)
  }

  showSourcesOfList = (item) => () => {
    this.props.actions.showSourcesOfList(item)
  }

  onShareList = (id) => () => {
    this.props.actions.shareSourceList(id)
  }

  onUnshareList = (id) => () => {
    this.props.actions.unshareSourceList(id)
  }

  getColumns() {
    const { t } = this.props

    let columns = [
      {
        Header: <SortableTh title="sourceListsTab.tableLabels.name" />,
        accessor: 'name',
        Cell: ({ original }) => {
          return (
            <a
              href="#"
              onClick={this.showSourcesOfList(original)}
            >
              {original.name}
            </a>
          )
        }
      },
      {
        id: 'sources',
        Header: <SortableTh title="sourceListsTab.tableLabels.sources" />,
        accessor: (item) => item.sourceNumber
      },
      {
        id: 'createdBy',
        Header: <SortableTh title="sourceListsTab.tableLabels.createdBy" />,
        accessor: (item) => `${item.user.firstName} ${item.user.lastName}`
      },
      {
        id: 'lastUpdated',
        Header: <SortableTh title="sourceListsTab.tableLabels.lastUpdated" />,
        accessor: (item) =>
          item.updatedAt && moment(item.updatedAt).format('Do MMM YYYY')
      },
      {
        id: 'lastUpdatedBy',
        Header: <SortableTh title="sourceListsTab.tableLabels.lastUpdatedBy" />,
        accessor: (item) =>
          item.updatedBy &&
          `${item.updatedBy.firstName} ${item.updatedBy.lastName}`
      },
      {
        id: 'action',
        Header: t('sourceIndexTab.action'),
        // sortable: false,
        minWidth: 220,
        Cell: ({ original }) => {
          return (
            // <UncontrolledButtonDropdown>
            //   <DropdownToggle
            //     // caret
            //     // className="btn-icon btn-icon-only btn btn-link"
            //     color="link"
            //   >
            //     <i className="lnr-menu-circle btn-icon-wrapper" />
            //   </DropdownToggle>
            //   <DropdownMenu>
            //     <h2>Hello</h2>
            //     {/* <DropdownItem onClick={this.onUnshareList(original.id)}>
            //         <i className="dropdown-icon lnr-inbox"> </i>
            //         <span>{t("sourceListsTab.unshare")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.onShareList(original.id)}>
            //         <i className="dropdown-icon lnr-file-empty"> </i>
            //         <span>{t("sourceListsTab.share")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.showRenameListPopup(original)}>
            //         <i className="dropdown-icon lnr-book"> </i>
            //         <span>{t("sourceListsTab.rename")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.showCloneListPopup(original)}>
            //         <i className="dropdown-icon lnr-picture"> </i>
            //         <span>{t("sourceListsTab.clone")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.showDeleteListPopup(original)}>
            //         <i className="dropdown-icon lnr-picture"> </i>
            //         <span>{t("sourceListsTab.delete")}</span>
            //       </DropdownItem> */}
            //   </DropdownMenu>
            // </UncontrolledButtonDropdown>

            // <div className="d-block w-100 text-center">
            //   <UncontrolledButtonDropdown>
            //     <DropdownToggle
            //       caret
            //       className="btn-icon btn-icon-only btn btn-link"
            //       color="link"
            //     >
            //       <i className="lnr-menu-circle btn-icon-wrapper" />
            //     </DropdownToggle>
            //     <DropdownMenu className="rm-pointers dropdown-menu-hover-link">
            //       <DropdownItem onClick={this.onUnshareList(original.id)}>
            //         <i className="dropdown-icon lnr-inbox"> </i>
            //         <span>{t("sourceListsTab.unshare")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.onShareList(original.id)}>
            //         <i className="dropdown-icon lnr-file-empty"> </i>
            //         <span>{t("sourceListsTab.share")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.showRenameListPopup(original)}>
            //         <i className="dropdown-icon lnr-book"> </i>
            //         <span>{t("sourceListsTab.rename")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.showCloneListPopup(original)}>
            //         <i className="dropdown-icon lnr-picture"> </i>
            //         <span>{t("sourceListsTab.clone")}</span>
            //       </DropdownItem>
            //       <DropdownItem onClick={this.showDeleteListPopup(original)}>
            //         <i className="dropdown-icon lnr-picture"> </i>
            //         <span>{t("sourceListsTab.delete")}</span>
            //       </DropdownItem>
            //     </DropdownMenu>
            //   </UncontrolledButtonDropdown>
            // </div>
            <div>
              <Button
                outline
                size="sm"
                color="info"
                className="border-0"
                onClick={
                  original.shared
                    ? this.onUnshareList(original.id)
                    : this.onShareList(original.id)
                }
              >
                {original.shared
                  ? t('sourceListsTab.unshare')
                  : t('sourceListsTab.share')}
              </Button>
              <Button
                outline
                size="sm"
                color="info"
                className="border-0"
                onClick={this.showRenameListPopup(original)}
              >
                {t('sourceListsTab.rename')}
              </Button>
              <Button
                outline
                size="sm"
                color="info"
                className="border-0"
                onClick={this.showCloneListPopup(original)}
              >
                {t('sourceListsTab.clone')}
              </Button>
              <Button
                outline
                size="sm"
                color="secondary"
                className="border-0"
                onClick={this.showDeleteListPopup(original)}
              >
                {t('sourceListsTab.delete')}
              </Button>
            </div>
          )
        }
      }
    ]

    const cols = [
      'name',
      'sources',
      'createdBy',
      'lastUpdated',
      'lastUpdatedBy',
      'action'
    ]
    return columns.filter(
      (col) => cols.includes(col.id) || cols.includes(col.accessor)
    )
  }

  render() {
    const { tableState } = this.props
    const columns = this.getColumns()

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
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SourceListsTable)
