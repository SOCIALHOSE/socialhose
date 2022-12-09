import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import FiltersTable from '../../../../common/FiltersTable/FiltersTable'
import { Button } from 'reactstrap'

export class RefinePanel extends React.Component {

  static propTypes = {
    t: PropTypes.func.isRequired,
    advancedFilters: PropTypes.object.isRequired,
    selectedFilters: PropTypes.object.isRequired,
    clearPending: PropTypes.object.isRequired,
    filterPages: PropTypes.object.isRequired,
    onRefine: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired
  };

  onHiderClick = (e) => {
    e.preventDefault()
    this.props.actions.toggleRefinePanel()
  };

  onSelectFilter = (groupName, filterValue) => {
    this.props.actions.selectRefineFilter(groupName, filterValue)
  };

  onClearFilters = (groupName) => {
    this.props.actions.clearRefineFilters(groupName)
  };

  onClearAllFilters = () => {
    this.props.actions.clearAllRefineFilters()
  };

  onMoreFilters = (groupName) => {
    this.props.actions.loadMoreRefineFilters(groupName)
  };

  onLessFilters = (groupName) => {
    this.props.actions.loadLessRefineFilters(groupName)
  };

  /*  onPressEnter = (e) => {
    if (e.keyCode === 13) {
      const keyword = document.getElementById('refine-keyword').value
      this.props.actions.selectRefineFilter('keyword', keyword)
      setTimeout(() => {
        this.props.onRefine()
      })
    }
  }; */

  render () {
    return (
      <div className="refine-panel px-4">
        <Button
          color="light"
          title="Hide refine panel"
          className="d-block ml-auto mb-3 btn-icon"
          onClick={this.onHiderClick}
          >
            {this.props.t('searchTab.hide')}
        </Button>
        {/* <Input
          type="text"
          className="mb-2"
          id="refine-keyword"
          placeholder={this.props.t('common:advancedFilters.keywordRefine')}
          onKeyUp={this.onPressEnter}
        /> */}
        <FiltersTable
          filters={this.props.advancedFilters}
          selectedFilters={this.props.selectedFilters}
          clearPending={this.props.clearPending}
          pages={this.props.filterPages}
          callbacks={{
            'selectFilter': this.onSelectFilter,
            'clearFilters': this.onClearFilters,
            'clearAllFilters': this.onClearAllFilters,
            'moreFilters': this.onMoreFilters,
            'lessFilters': this.onLessFilters,
            'refine': this.props.onRefine
          }}
        />

      </div>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(RefinePanel)

