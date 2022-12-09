import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Button, Input, InputGroup, InputGroupAddon } from 'reactstrap'
export class SearchingBlock extends React.Component {
  static propTypes = {
    searchResultsErrors: PropTypes.array.isRequired,
    loadedFeedQuery: PropTypes.string,
    onSearchQuery: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  }

  onPressEnter = (e) => {
    if (e.keyCode === 13) {
      this.props.onSearchQuery()
    }
  }

  onChangeQuery = (e) => {
    const { actions } = this.props
    const value = e.target.value;
    // replace smart quotation marks with normal
    let filterQuotes = value.replace(/[\u2018\u2019]/g, '\'').replace(/[\u201C\u201D]/g, '"')
    // add space before operator if not
    filterQuotes = filterQuotes.replace(/\s*\+/g, ' +').replace(/\s*\-/g, ' -').trimStart()
    actions.changeFeedQuery(filterQuotes)
  }

  render() {
    let { t, loadedFeedQuery } = this.props
    loadedFeedQuery = loadedFeedQuery || ''

    return (
      <div className="search-input-field mb-2">
        <InputGroup>
          <Input
            type="text"
            value={loadedFeedQuery}
            data-tour="input-field-search"
            onChange={this.onChangeQuery}
            placeholder={t('searchTab.searchInputPlaceholder')}
            onKeyUp={this.onPressEnter}
          />
          <InputGroupAddon addonType="append">
            <Button
              color="primary"
              className="btn-icon btn-icon-only px-3"
              data-tour="search-button"
              onClick={this.props.onSearchQuery}
            >
              <i className="lnr-magnifier btn-icon-wrapper font-weight-bold"></i>
            </Button>
          </InputGroupAddon>
        </InputGroup>
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SearchingBlock)
