import React from 'react'
import PropTypes from 'prop-types'
import SearchingResultsTopPanel from './SearchingResultsTopPanel'
import Article from './Article'
import DeleteArticlesPopup from './DeleteArticlesPopup'
import EmailArticlesPopup from './EmailArticlesPopup'
import CommentArticlePopup from './CommentArticlePopup'
import ClipArticlesPopup from './ClipArticles/ClipArticlesPopup'
import Pager from '../../../../common/Pager/Pager'
import EmailConfirmPopup from './EmailConfirmPopup'
import NoRecords from '../../../../common/NoRecords'
import Loading from '../../../../common/Loading'
import { Interpolate, translate } from 'react-i18next'

export class SearchingResults extends React.Component {
  static propTypes = {
    searchState: PropTypes.object.isRequired,
    articlesState: PropTypes.object.isRequired,
    isRefinePanelVisible: PropTypes.bool.isRequired,
    toggleRefinePanel: PropTypes.func.isRequired,
    onPager: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  forEachArticle = (cb) => {
    const { searchState, articlesState } = this.props
    return searchState.searchResults
      .filter((article) => !articlesState.excludedArticles.includes(article.id))
      .map(cb)
  };

  render () {
    const { searchState, articlesState, actions, t } = this.props
    const isSearchResultsLoaded = searchState.searchResults.length > 0
    const numPages = Math.ceil(
      searchState.searchResultTotalCount / searchState.searchResultLimit
    )

    const noRecords = searchState.searchResultsPending || !isSearchResultsLoaded || !searchState.isSynced

    if (searchState.searchResultsPending) {
      return (
        <div className="search-results">
          <Loading />
        </div>
      )
    }

    if (!searchState.isSynced) {
      return (
        <div className="search-results">
          <NoRecords message={t('searchTab.notSynchronized')} />
        </div>
      )
    }

    if (searchState.isSynced && !isSearchResultsLoaded) {
      return (
        <div className="search-results">
          <NoRecords message={t('searchTab.noResults')} />
        </div>
      )
    }

    return (
      <div className="search-results">       
        <SearchingResultsTopPanel
          noRecords={noRecords}
          searchResultsCount={searchState.searchResults.length}
          selectedArticles={searchState.selectedArticles}
          selectAllArticles={actions.selectAllArticles}
          showDeleteArticlesPopup={actions.showDeleteArticlesPopup}
          showEmailArticlesPopup={actions.showEmailArticlesPopup}
          showClipArticlesPopup={actions.showClipArticlesPopup}
          isRefinePanelVisible={noRecords ? false : this.props.isRefinePanelVisible}
          toggleRefinePanel={this.props.toggleRefinePanel}
        />

        {isSearchResultsLoaded &&
          <p className="text-muted font-size-xs">
            <Interpolate
              t={t}
              i18nKey="searchTab.articlesCountDivider"
              resultsCount={searchState.searchResultCount}
              totalCount={searchState.searchResultTotalCount}
            />
          </p>
        }
        <div className="search-results-block mt-1">
          {isSearchResultsLoaded &&
            this.forEachArticle((article, i) => {
              return (
                <Article
                  key={'article-' + i}
                  article={article}
                  selectedArticles={searchState.selectedArticles}
                  selectArticle={actions.selectArticle}
                  showDeletePopup={actions.showDeleteArticlesPopup}
                  showEmailPopup={actions.showEmailArticlesPopup}
                  showCommentPopup={actions.showCommentArticlePopup}
                  showClipPopup={actions.showClipArticlesPopup}
                  deleteComment={actions.deleteComment}
                  readArticleLater={actions.readArticleLater}
                  loadMoreComments={actions.loadMoreComments}
                  showShareMenu={actions.showShareMenu}
                />
              )
            })}

          {isSearchResultsLoaded && (
            <Pager
              pagerAction={this.props.onPager}
              currentPage={searchState.searchResultPage}
              numPages={numPages}
              limitByPage={searchState.searchResultLimit}
              hideLimitSelector
            />
          )}
        </div>

        {articlesState.deletePopup.visible && (
          <DeleteArticlesPopup
            articles={articlesState.deletePopup.articles}
            hidePopup={actions.hideDeleteArticlesPopup}
            activeFeed={searchState.activeFeed}
            deleteArticles={actions.deleteArticles}
            deleteArticlesFromFeed={actions.deleteArticlesFromFeed}
            addAlert={actions.addAlert}
          />
        )}

        {articlesState.emailPopup.visible && (
          <EmailArticlesPopup
            articlesToEmail={articlesState.emailPopup.articles}
            emailArticles={actions.emailArticles}
            hidePopup={actions.hideEmailArticlesPopup}
            addAlert={actions.addAlert}
            loadRecipients={actions.loadRecipients}
            recipients={articlesState.emailPopup.recipients}
          >
            {articlesState.emailConfirmPopup.visible && (
              <EmailConfirmPopup
                hidePopup={actions.hideEmailConfirmPopup}
                hideEmailPopup={actions.hideEmailArticlesPopup}
                sendDocumentsByEmail={actions.sendDocumentsByEmail}
              />
            )}
          </EmailArticlesPopup>
        )}

        {articlesState.commentPopup.visible && (
          <CommentArticlePopup
            article={articlesState.commentPopup.article}
            comment={articlesState.commentPopup.comment}
            commentArticle={actions.commentArticle}
            updateComment={actions.updateComment}
            hidePopup={actions.hideCommentArticlePopup}
            addAlert={actions.addAlert}
          />
        )}

        {articlesState.clipPopup.visible && (
          <ClipArticlesPopup
            articles={articlesState.clipPopup.articles}
            recentClipFeeds={articlesState.recentClipFeeds}
            getRecentClipFeeds={actions.getRecentClipFeeds}
            hidePopup={actions.hideClipArticlesPopup}
            clipArticles={actions.clipArticles}
            addAlert={actions.addAlert}
          />
        )}
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SearchingResults)
