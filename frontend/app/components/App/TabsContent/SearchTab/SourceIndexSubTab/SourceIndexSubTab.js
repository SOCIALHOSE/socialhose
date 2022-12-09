import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import SourceIndexTable from './SourceIndexTable'
import SourceIndexUpdatePopup from './SourceIndexUpdatePopup'
import FiltersTable from '../../../../common/FiltersTable/FiltersTable'
import { withRouter } from 'react-router-dom'
import reduxConnect from '../../../../../redux/utils/connect'
import { compose } from 'redux'
import { Button, ButtonGroup, Input, InputGroup, InputGroupAddon } from 'reactstrap'
import { setDocumentData } from '../../../../../common/helper'

class SourceIndexSubTab extends React.Component {
  static propTypes = {
    sourcesState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  componentDidMount() {
    setDocumentData('title', 'Source Index | Search')
  }

  componentWillUnmount() {
    setDocumentData('title')
  }

  _sourceIndexesState = () => this.props.sourcesState.sourceIndexesState;
  _sourceLists = () => this.props.sourcesState.sourceListsState.data;

  loadSourceIndexes = (params) => {
    this.props.actions.getSourceIndexes(params || null)
  };

  onSearchSources = () => {
    this.loadSourceIndexes()
  };

  onEnterSearchInput = (e) => {
    if (e.keyCode === 13) this.loadSourceIndexes()
  };

  onChangeSearchInput = (e) => {
    this.props.actions.setSourceIndexSearchQuery(e.target.value)
  };

  onFetchData = (params) => {
    this.loadSourceIndexes(params)
  };

  showAddToListPopup = () => {
    const { actions } = this.props
    const sourceIndexesState = this._sourceIndexesState()
    if (sourceIndexesState.selectedIds.length === 0) {
      actions.addAlert({
        type: 'notice',
        transKey: 'noListsSelected',
        id: 'noListsSelected'
      })

      return false
    }

    actions.toggleAddSourceToListPopup()
  };

  onSelectFilter = (groupName, filterValue) => {
    this.props.actions.selectSourcesFilter(groupName, filterValue)
  };

  onClearFilters = (groupName) => {
    this.props.actions.clearSourcesFilters(groupName)
  };

  onClearAllFilters = () => {
    this.props.actions.clearAllSourcesFilters()
  };

  onMoreFilters = (groupName) => {
    this.props.actions.loadMoreSourcesFilters(groupName)
  };

  onLessFilters = (groupName) => {
    this.props.actions.loadLessSourcesFilters(groupName)
  };

  render () {
    const { t, actions } = this.props
    const sourceIndexesState = this._sourceIndexesState()
    const sourceLists = this._sourceLists()
    const {
      searchQuery,
      selectedIds,
      chosenListsToAddSources,
      chosenSourceToUpdate,
      advancedFilters
    } = sourceIndexesState

    return (
      <div className="mb-3">
        <InputGroup className="mb-3">
          <Input
            type="text"
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

        <ButtonGroup className="mb-3">
          <Button
            onClick={this.showAddToListPopup}
            color="secondary"
          >
            <i className="fa fa-plus fa-1px for-small mr-1"> </i>{" "}
            {t('sourceIndexTab.addToSourceListsBtn')}
          </Button>
        </ButtonGroup>

        <div className="search-content">
          <SourceIndexTable
            tableState={sourceIndexesState}
            type="sourceIndexesState"
            onFetch={this.onFetchData}
            actions={actions}
          />

          <FiltersTable
            filters={advancedFilters.all}
            pages={advancedFilters.pages}
            selectedFilters={advancedFilters.selected}
            clearPending={advancedFilters.pending}
            callbacks={{
              selectFilter: this.onSelectFilter,
              clearFilters: this.onClearFilters,
              clearAllFilters: this.onClearAllFilters,
              moreFilters: this.onMoreFilters,
              lessFilters: this.onLessFilters,
              refine: this.onSearchSources
            }}
          />
        </div>

        {sourceIndexesState.isAddPopupVisible && (
          <SourceIndexUpdatePopup
            type="add"
            sourceLists={sourceLists}
            chosenLists={chosenListsToAddSources}
            chosenSourceIndexes={selectedIds}
            actions={actions}
          />
        )}

        {sourceIndexesState.isUpdatePopupVisible && (
          <SourceIndexUpdatePopup
            type="update"
            sourceLists={sourceLists}
            chosenLists={chosenSourceToUpdate.listIds}
            chosenSourceIndexes={[chosenSourceToUpdate.id]}
            updateItemTitle={chosenSourceToUpdate.title}
            actions={actions}
          />
        )}
      </div>
    )
  }
}

const applyDecorators = compose(
  withRouter,
  reduxConnect('sourcesState', ['appState', 'sourcesState']),
  translate(['tabsContent'], { wait: true })
)

export default applyDecorators(SourceIndexSubTab)
