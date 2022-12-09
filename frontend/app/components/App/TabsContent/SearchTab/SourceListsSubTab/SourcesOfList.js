import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import SourceIndexTable from '../SourceIndexSubTab/SourceIndexTable'
import SourceListsDeletePopup from './SourceListsDeletePopup'
import { Button, Input, InputGroup, InputGroupAddon } from 'reactstrap'

export class SourcesOfList extends React.Component {
  static propTypes = {
    sourcesOfListState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  componentWillMount = () => {
    this.searchSources('')
  };

  searchSources = (query) => {
    const { actions, sourcesOfListState } = this.props

    actions.getSourcesOfList(sourcesOfListState.visibleList.id, {
      query: query,
      page: sourcesOfListState.page,
      limit: sourcesOfListState.limit
    })
  };

  onSearchSources = () => {
    const query = this.props.sourcesOfListState.searchQuery
    this.searchSources(query)
  };

  onEnterSearchInput = (e) => {
    if (e.keyCode === 13) this.onSearchSources()
  };

  onChangeSearchInput = (e) => {
    const val = e.target.value
    this.props.actions.setSourcesOfListSearchQuery(val)
  };

  onFetchData = (params) => {
    const { sourcesOfListState, actions } = this.props
    actions.getSourcesOfList(sourcesOfListState.visibleList.id, params)
  };

  onDeleteIndex = (source) => {
    const { sourcesOfListState, actions } = this.props
    const listId = sourcesOfListState.visibleList.id

    actions.updateListSources({
      id: source.id,
      sourceLists: source.listIds.filter(id => id !== listId)
    })
  };

  render () {
    const { t, sourcesOfListState, actions } = this.props
    const { searchQuery, visibleList, isDeletePopupVisible, listToEdit } = sourcesOfListState

    return (
      <div>
        <Button className="btn-wide mb-3" size="sm" color="info" onClick={actions.hideSourcesOfList}>
          <i className="lnr lnr-chevron-left"> </i>
        </Button>

        <div className="mb-3">
          <p className="text-primary text-uppercase font-weight-bold mb-2">{visibleList.name} ({visibleList.sourceNumber})</p>
          <InputGroup>
            <Input
              id="source-index-search"
              placeholder={t('sourceIndexTab.mainInputPlaceholder')}
              value={searchQuery}
              onChange={this.onChangeSearchInput}
              onKeyUp={this.onEnterSearchInput}
            />
            <InputGroupAddon addonType="append">
              <Button
                color="primary"
                className="btn-icon btn-icon-only"
                onClick={this.onSearchSources}
              >
                <i className="lnr-magnifier btn-icon-wrapper"></i>
              </Button>
            </InputGroupAddon>
          </InputGroup>
        </div>

        <SourceIndexTable
          tableState={sourcesOfListState}
          type='sourcesOfListState'
          onFetch={this.onFetchData}
          onDeleteIndex={actions.toggleDeleteListIndexPopup}
          actions={actions}
        />

        {isDeletePopupVisible &&
          <SourceListsDeletePopup
            listToEdit={listToEdit}
            toggleDeleteListPopup={actions.toggleDeleteListIndexPopup}
            deleteSourceList={this.onDeleteIndex}
          />
        }
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SourcesOfList)

