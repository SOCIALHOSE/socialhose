import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import SaveFeedPopup from './SaveFeedPopup'
import { Button } from 'reactstrap'
export class SearchSubTabHead extends React.Component {
  static propTypes = {
    feedCategories: PropTypes.array.isRequired,
    isSaveFeedPopupVisible: PropTypes.bool.isRequired,
    activeFeed: PropTypes.object,
    isSaving: PropTypes.bool.isRequired,
    isEditingFeed: PropTypes.bool.isRequired,
    addAlert: PropTypes.func.isRequired,
    toggleSaveFeedPopup: PropTypes.func.isRequired,
    onSaveAsFeed: PropTypes.func.isRequired,
    getSidebarCategories: PropTypes.func.isRequired,
    editFeed: PropTypes.func.isRequired,
    setNewSearch: PropTypes.func.isRequired,
    renewSearchBy: PropTypes.func.isRequired,
    changeActiveFeedName: PropTypes.func.isRequired,
    saveFeed: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  openSaveFeedPopup = () => {
    this.props.toggleSaveFeedPopup()
  }

  saveFeed = () => {
    this.props.saveFeed()
  }

  onEditFeed = () => {
    this.props.editFeed()
  }

  onNewSearch = () => {
    this.props.setNewSearch()
    this.props.renewSearchBy()
  }

  onChangeFeedName = (event) => {
    this.props.changeActiveFeedName(event.target.value)
  }

  render() {
    const {
      t,
      isEditingFeed,
      isSaveFeedPopupVisible,
      isSaving,
      activeFeed
    } = this.props
    const feedIsLoaded = !!activeFeed
    const showEditButton =
      !!activeFeed && !isEditingFeed && activeFeed.subType === 'query_feed'

    return (
      <div>
        <div className="d-flex flex-wrap justify-content-between">
          <div>
            {!isEditingFeed && activeFeed && <h4 className="text-primary mb-2 mb-md-0">{activeFeed.name}</h4>}
          </div>
          <div className="text-right" data-tour="search-buttons">
            <Button
              className="btn-icon mb-2 mb-lg-0 ml-2"
              color="primary"
              onClick={this.onNewSearch}
            >
              <i className="lnr-plus-circle btn-icon-wrapper"></i>
              {t('searchTab.newSearchBtn')}
            </Button>

            {!feedIsLoaded && (
              <Button
                className="btn-icon mb-2 mb-lg-0 ml-2"
                color="success"
                onClick={this.openSaveFeedPopup}
              >
                <i className="lnr-checkmark-circle btn-icon-wrapper"></i>
                {isSaving ? t('searchTab.savingBtn') : t('searchTab.saveBtn')}
              </Button>
            )}

            {feedIsLoaded && isEditingFeed && (
              <Button
                className="btn-icon mb-2 mb-lg-0 ml-2"
                color="success"
                onClick={this.saveFeed}
              >
                <i className="lnr-checkmark-circle btn-icon-wrapper"></i>
                {isSaving ? t('searchTab.savingBtn') : t('searchTab.saveBtn')}
              </Button>
            )}

            {feedIsLoaded && isEditingFeed && (
              <Button
                className="btn-icon mb-2 mb-lg-0 ml-2"
                color="success"
                onClick={this.openSaveFeedPopup}
              >
                <i className="lnr-checkmark-circle btn-icon-wrapper"></i>
                {t('searchTab.saveAsBtn')}
              </Button>
            )}

            {showEditButton && (
              <Button
                className="btn-icon mb-2 mb-lg-0 ml-2"
                color="warning"
                onClick={this.onEditFeed}
              >
                <i className="lnr-pencil btn-icon-wrapper"></i>
                {t('searchTab.editFeedBtn')}
              </Button>
            )}
          </div>
        </div>

        {isSaveFeedPopupVisible && (
          <SaveFeedPopup
            saveType="typeSaveAs"
            feedCategories={this.props.feedCategories}
            toggleSaveFeedPopup={this.props.toggleSaveFeedPopup}
            addAlert={this.props.addAlert}
            onSaveAsFeed={this.props.onSaveAsFeed}
            getSidebarCategories={this.props.getSidebarCategories}
          />
        )}
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SearchSubTabHead)
