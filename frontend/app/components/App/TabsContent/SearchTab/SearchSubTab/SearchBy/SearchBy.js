import React from 'react';
import PropTypes from 'prop-types';
import SearchByTabs from './SearchByTabs';
import EmphasisTab from './EmphasisTab';
import LangsTab from './LangsTab';
import LocationsTab from './LocationsTab';
import SourcesTab from './SourcesTab';
import SourceListsTab from './SourceListsTab';
import DuplicatesTab from './DuplicatesTab';
import ExtrasTab from './ExtrasTab';
import { translate } from 'react-i18next';
import { Button, Container, Row } from 'reactstrap';

export class SearchBy extends React.Component {
  static propTypes = {
    userSubscriptionDate: PropTypes.string.isRequired,
    userSubscription: PropTypes.string.isRequired,
    searchByFiltersState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.state = {
      animationDisabled: true,
      arrowPosition: true
    };
  }

  onToggleSearchBy = () => {
    this.props.actions.toggleSearchBy();
  };

  render() {
    const { t } = this.props;
    const { searchByFiltersState, actions } = this.props;
    const visibleClass = searchByFiltersState.isSearchByVisible
      ? ' visible'
      : ' closed';

    return (
      <div
        className={'search-by-container mb-3 mb-md-0' + visibleClass}
        data-tour="advanced-search"
      >
        <div className="search-by">
          <SearchByTabs
            searchByTabs={searchByFiltersState.searchByTabs}
            chooseSearchByTab={actions.chooseSearchByTab}
            chosenSearchByTab={searchByFiltersState.chosenSearchByTab}
          />

          <Container fluid>
            <Row className="mb-3" data-tour="advanced-search-content">
              {searchByFiltersState.chosenSearchByTab === 'emphasis' && (
                <EmphasisTab
                  include={searchByFiltersState.headlineIncluded}
                  exclude={searchByFiltersState.headlineExcluded}
                  setHeadlineIncluded={actions.setHeadlineIncluded}
                  setHeadlineExcluded={actions.setHeadlineExcluded}
                />
              )}

              {searchByFiltersState.chosenSearchByTab === 'languages' && (
                <LangsTab
                  searchLanguages={searchByFiltersState.searchLanguages}
                  chosenLanguages={searchByFiltersState.chosenLanguages}
                  toggleLang={actions.toggleLang}
                  toggleAllLangs={actions.toggleAllLangs}
                />
              )}

              {searchByFiltersState.chosenSearchByTab === 'locations' && (
                <LocationsTab
                  locations={searchByFiltersState.locations}
                  chosenLocationsType={searchByFiltersState.chosenLocationsType}
                  locationsToInclude={searchByFiltersState.locationsToInclude}
                  locationsToExclude={searchByFiltersState.locationsToExclude}
                  changeLocationsType={actions.changeLocationsType}
                  moveLocation={actions.moveLocation}
                  clearLocations={actions.clearLocations}
                />
              )}

              {searchByFiltersState.chosenSearchByTab === 'sources' && (
                <SourcesTab
                  chosenMediaTypes={searchByFiltersState.chosenMediaTypes}
                  chosenLanguages={searchByFiltersState.chosenLanguages}
                  searchBySources={searchByFiltersState.searchBySources}
                  searchBySourcesType={searchByFiltersState.searchBySourcesType}
                  selectedSearchBySources={
                    searchByFiltersState.selectedSearchBySources
                  }
                  searchBySourcesQuery={
                    searchByFiltersState.searchBySourcesQuery
                  }
                  setSearchBySourcesQuery={actions.setSearchBySourcesQuery}
                  getSearchBySources={actions.getSearchBySources}
                  addSelectedSearchBySource={actions.addSelectedSearchBySource}
                  removeSelectedSearchBySource={
                    actions.removeSelectedSearchBySource
                  }
                  clearSearchBySources={actions.clearSearchBySources}
                  includeExcludeSearchBySources={
                    actions.includeExcludeSearchBySources
                  }
                />
              )}

              {searchByFiltersState.chosenSearchByTab === 'sourceLists' && (
                <SourceListsTab
                  searchBySourceLists={
                    searchByFiltersState.searchBySourceListsAvailable
                  }
                  searchBySourceListsToInclude={
                    searchByFiltersState.searchBySourceListsToInclude
                  }
                  searchBySourceListsToExclude={
                    searchByFiltersState.searchBySourceListsToExclude
                  }
                  getSourceLists={actions.getSearchBySourceLists}
                  moveSourceList={actions.moveSourceList}
                />
              )}

              {searchByFiltersState.chosenSearchByTab === 'duplicates' && (
                <DuplicatesTab
                  includeDuplicates={searchByFiltersState.includeDuplicates}
                  toggleIncludeDuplicates={actions.toggleIncludeDuplicates}
                />
              )}

              {searchByFiltersState.chosenSearchByTab === 'extras' && (
                <ExtrasTab
                  hasImages={searchByFiltersState.hasImages}
                  toggleHasImages={actions.toggleHasImages}
                />
              )}
            </Row>
          </Container>
        </div>
        <hr className="mt-0 mb-2" />
        <Button
          outline
          size="sm"
          className="font-size-xs"
          color="secondary"
          onClick={this.onToggleSearchBy}
        >
          {t('searchTab.searchBySection.searchByBtn')}
          {searchByFiltersState.isSearchByVisible ? (
            <i className="lnr-chevron-up btn-icon-wrapper"></i>
          ) : (
            <i className="lnr-chevron-down btn-icon-wrapper"></i>
          )}
        </Button>
      </div>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(SearchBy);
