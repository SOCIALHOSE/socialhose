import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import SearchSubTabHead from './SearchSubTabHead';
import MediaTypes from './MediaTypes';
import SearchingBlock from './SearchingBlock';
import SearchingResults from './SearchingResults';
import SearchBy from './SearchBy/SearchBy';
import RefinePanel from './RefinePanel';
import Restrictions from '../../../../common/Restrictions/Restrictions';
import { parseSearchDays } from '../../../../../common/Common';
import reduxConnect from '../../../../../redux/utils/connect';
import { Card, CardBody, CardTitle } from 'reactstrap';
import { setDocumentData } from '../../../../../common/helper';
import { translate } from 'react-i18next';
import { compose } from 'redux';

export const domainNames = ['reddit', 'twitter', 'instagram'];

class SearchSubTab extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    store: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

  get searchState() {
    return this.props.store.appState.search;
  }
  get searchByFiltersState() {
    return this.props.store.appState.searchByFilters;
  }
  get articlesState() {
    return this.props.store.appState.articles;
  }
  get authState() {
    return this.props.store.common.auth;
  }

  componentDidMount() {
    setDocumentData('title', 'Search');
  }

  componentWillUnmount() {
    setDocumentData('title');
  }

  _sendSearchQuery = (page, initialSearch = false) => {
    const { actions } = this.props;
    const dataToSend = this.gatherSearchQueryData();
    if (dataToSend) {
      dataToSend.page = page;
      dataToSend.advancedFilters = this.gatherAdvancedFilters();
      actions.getSearchResults(dataToSend, initialSearch);
    }
  };

  _sendFeedQuery = (page, activeFeed) => {
    const { actions } = this.props;
    const params = {
      page: page,
      advancedFilters: this.gatherAdvancedFilters()
    };
    actions.getFeedResults(params, activeFeed.id);
  };

  onSearchQuery = () => {
    this._sendSearchQuery(1, true);
  };

  onRefine = () => {
    const { activeFeed } = this.searchState;
    if (activeFeed) {
      this._sendFeedQuery(1, activeFeed);
    } else {
      this._sendSearchQuery(1);
    }
  };

  onPager = ({ currentPage: page }) => {
    const { activeFeed } = this.searchState;
    if (activeFeed) {
      this._sendFeedQuery(page, activeFeed);
    } else {
      this._sendSearchQuery(page);
    }
  };

  onSaveAsFeed = (name, category) => {
    const dataToSend = this.getFeedData(name, category, 'query_feed');
    dataToSend && this.props.actions.saveAsFeed(dataToSend);
  };

  onSaveFeed = () => {
    const { actions } = this.props;
    const { activeFeed } = this.searchState;
    const dataToSend = this.getFeedData(
      activeFeed.name,
      activeFeed.category,
      activeFeed.subType
    );
    dataToSend && actions.saveFeed(dataToSend, activeFeed.id);
  };

  getFeedData = (name, category, feedSubType) => {
    let dataToSend = {};

    const searchQueryData = this.gatherSearchQueryData();

    if (!searchQueryData) return false;

    dataToSend.search = searchQueryData;
    dataToSend.search.advancedFilters = this.gatherAdvancedFilters();

    dataToSend.feed = {
      name: name,
      category: category,
      subType: feedSubType
    };

    const excludedArticles = this.articlesState.excludedArticles;
    if (excludedArticles && excludedArticles.length) {
      dataToSend.feed.excludedDocuments = excludedArticles;
    }

    return dataToSend;
  };

  gatherSearchQueryData = () => {
    const searchState = this.searchState;
    const searchByFiltersState = this.searchByFiltersState;
    const { userSubscription } = this.authState;
    const { actions } = this.props;

    let dataToSend = {};

    const query = searchState.loadedFeedQuery;

    if (!query) {
      actions.addAlert({ type: 'error', transKey: 'searchQueryEmpty' });
      return false;
    }

    dataToSend.query = query;

    dataToSend.filters = {}; //create filters prop

    //setting media types filter
    if (searchByFiltersState.chosenMediaTypes.length) {
      const source = [];
      const domain = [];
      searchByFiltersState.chosenMediaTypes.map((v) => {
        if (domainNames.includes(v)) {
          domain.push(`${v}.com`);
        } else {
          source.push(v);
        }
      });
      dataToSend.filters.publisher = { source, domain };
    } else {
      actions.addAlert({ type: 'error', transKey: 'noMediaTypesSelected' });
      return false;
    }

    // setting date filter
    const chosenInterval = searchByFiltersState.chosenSearchInterval;
    const chosenStartDate = searchByFiltersState.chosenStartDate;
    const chosenEndDate = searchByFiltersState.chosenEndDate;

    if (chosenInterval === 'between') {
      if (chosenStartDate !== '' || chosenEndDate !== '') {
        dataToSend.filters.date = {
          type: 'between',
          start: chosenStartDate,
          end: chosenEndDate
        };
      } else {
        dataToSend.filters.date = {
          type: 'last',
          days:
            searchByFiltersState.chosenSearchDate === 'all'
              ? parseSearchDays(userSubscription)
              : parseSearchDays(searchByFiltersState.chosenSearchDate)
        };
      }
    } else if (chosenInterval === 'all') {
      dataToSend.filters.date = {
        type: 'last',
        days: parseSearchDays(userSubscription)
      };
    } else {
      dataToSend.filters.date = {
        type: 'last',
        days: parseSearchDays(searchByFiltersState.chosenSearchLastDate)
      };
    }

    //adding included or/and excluded headlines filter
    const headlineIncluded = searchByFiltersState.headlineIncluded;
    const headlineExcluded = searchByFiltersState.headlineExcluded;

    if (headlineIncluded.length || headlineExcluded.length) {
      dataToSend.filters.headline = {};
    }

    if (headlineIncluded.length) {
      dataToSend.filters.headline.include = headlineIncluded;
    }

    if (headlineExcluded.length) {
      dataToSend.filters.headline.exclude = headlineExcluded;
    }

    //setting languages filter
    const chosenLanguages = searchByFiltersState.chosenLanguages;

    if (chosenLanguages.length) {
      dataToSend.filters.language = chosenLanguages;
    }

    //setting locations filter
    const locationsToInclude = searchByFiltersState.locationsToInclude;
    const locationsToExclude = searchByFiltersState.locationsToExclude;

    const countriesToInclude = locationsToInclude.filter((loc) => {
      return loc.type === 'country';
    });
    const statesToInclude = locationsToInclude.filter((loc) => {
      return loc.type === 'state';
    });
    const countriesToExclude = locationsToExclude.filter((loc) => {
      return loc.type === 'country';
    });
    const statesToExclude = locationsToExclude.filter((loc) => {
      return loc.type === 'state';
    });

    if (countriesToInclude.length || countriesToExclude.length) {
      dataToSend.filters.country = {};
    }

    if (statesToInclude.length || statesToExclude.length) {
      dataToSend.filters.state = {};
    }

    if (countriesToInclude.length) {
      dataToSend.filters.country.include = countriesToInclude.map((loc) => {
        return loc.code;
      });
    }

    if (countriesToExclude.length) {
      dataToSend.filters.country.exclude = countriesToExclude.map((loc) => {
        return loc.code;
      });
    }

    if (statesToInclude.length) {
      dataToSend.filters.state.include = statesToInclude.map((loc) => {
        return loc.code;
      });
    }

    if (statesToExclude.length) {
      dataToSend.filters.state.exclude = statesToExclude.map((loc) => {
        return loc.code;
      });
    }

    //setting source filter
    const selectedSearchBySources =
      searchByFiltersState.selectedSearchBySources;
    if (selectedSearchBySources.length) {
      dataToSend.filters.source = {};
      dataToSend.filters.source.type = searchByFiltersState.searchBySourcesType;
      dataToSend.filters.source.ids = selectedSearchBySources.map((source) => {
        return source.id;
      });
    }

    //setting source lists filter
    const sourceListsToInclude =
      searchByFiltersState.searchBySourceListsToInclude;
    const sourceListsToExclude =
      searchByFiltersState.searchBySourceListsToExclude;

    if (sourceListsToInclude.length || sourceListsToExclude.length) {
      dataToSend.filters.sourceList = {};
    }

    if (sourceListsToInclude.length) {
      dataToSend.filters.sourceList.include = sourceListsToInclude.map(
        (source) => {
          return source.id;
        }
      );
    }

    if (sourceListsToExclude.length) {
      dataToSend.filters.sourceList.exclude = sourceListsToExclude.map(
        (source) => {
          return source.id;
        }
      );
    }

    //setting duplicates filter
    //dataToSend.filters.duplicates = searchByFiltersState.includeDuplicates;

    //setting 'has images' filter
    dataToSend.filters.hasImage = searchByFiltersState.hasImages;

    return dataToSend;
  };

  gatherAdvancedFilters = () => {
    return this.searchState.advancedFilters.selected;
  };

  render() {
    const searchState = this.searchState;
    const searchByFiltersState = this.searchByFiltersState;
    const {
      userSubscription,
      userSubscriptionDate,
      user: { restrictions }
    } = this.authState;
    const { store, actions } = this.props;
    const feedCategories = store.appState.sidebar.categories;
    const articlesState = store.appState.articles;

    const { advancedFilters } = searchState;
    const activeFeed = searchState.activeFeed;
    let isEditSearchVisible =
      !searchState.loadedFeedQuery || searchState.isEditingFeed;
    if (activeFeed && activeFeed.subType === 'clip_feed') {
      isEditSearchVisible = false;
    }
    const hasActiveFeed = !!activeFeed;

    return (
      <Fragment>
        {!hasActiveFeed && (
          <Restrictions
            restrictions={restrictions && restrictions.limits}
            restrictionsIds={['searchesPerDay', 'savedFeeds']}
          />
        )}
        <div className="search-tab">
          <Card className="main-card mb-3">
            <CardBody>
              <div className="search-block">
                {isEditSearchVisible && (
                  <div className="search-edit-block">
                    <SearchingBlock
                      searchResultsErrors={searchState.searchResultsErrors}
                      onSearchQuery={this.onSearchQuery}
                      loadedFeedQuery={searchState.loadedFeedQuery}
                      actions={actions}
                    />

                    <MediaTypes
                      mediaTypes={searchByFiltersState.mediaTypes}
                      chosenMediaTypes={searchByFiltersState.chosenMediaTypes}
                      actions={actions}
                      restrictions={restrictions}
                      searchByFiltersState={searchByFiltersState}
                      userSubscription={userSubscription}
                      userSubscriptionDate={userSubscriptionDate}
                      toggleMediaType={actions.toggleMediaType}
                      toggleAllMediaTypes={actions.toggleAllMediaTypes}
                    />

                    <SearchBy
                      userSubscription={userSubscription}
                      userSubscriptionDate={userSubscriptionDate}
                      searchByFiltersState={searchByFiltersState}
                      actions={actions}
                    />
                  </div>
                )}

                <SearchSubTabHead
                  isSaveFeedPopupVisible={searchState.isSaveFeedPopupVisible}
                  isSaving={searchState.isSavingFeed}
                  feedCategories={feedCategories}
                  onSaveAsFeed={this.onSaveAsFeed}
                  toggleSaveFeedPopup={actions.toggleSaveFeedPopup}
                  addAlert={actions.addAlert}
                  getSidebarCategories={actions.getSidebarCategories}
                  activeFeed={activeFeed}
                  isEditingFeed={searchState.isEditingFeed}
                  editFeed={actions.editFeed}
                  setNewSearch={actions.setNewSearch}
                  renewSearchBy={actions.renewSearchBy}
                  changeActiveFeedName={actions.changeActiveFeedName}
                  saveFeed={this.onSaveFeed}
                />
              </div>
            </CardBody>
          </Card>

          <Card className="main-card mb-3">
            <CardBody>
              <CardTitle>{this.props.t('searchTab.results')}</CardTitle>
              <div className="search-content">
                <SearchingResults
                  searchState={searchState}
                  articlesState={articlesState}
                  actions={actions}
                  isRefinePanelVisible={advancedFilters.isVisible}
                  toggleRefinePanel={actions.toggleRefinePanel}
                  onPager={this.onPager}
                />

                {searchState.isLoaded && advancedFilters.isVisible && (
                  <RefinePanel
                    advancedFilters={advancedFilters.all}
                    selectedFilters={advancedFilters.selected}
                    clearPending={advancedFilters.pending}
                    filterPages={advancedFilters.pages}
                    onRefine={this.onRefine}
                    actions={actions}
                  />
                )}
              </div>
            </CardBody>
          </Card>
        </div>
      </Fragment>
    );
  }
}

const applyDecorators = compose(
  reduxConnect(),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(SearchSubTab);
