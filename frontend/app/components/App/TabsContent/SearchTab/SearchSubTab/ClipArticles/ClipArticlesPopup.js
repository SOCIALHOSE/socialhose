import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import ClipDragSource from './ClipDragSource'
import RecentFeed from './RecentFeed'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'

export class ClipArticlesPopup extends React.Component {
  static propTypes = {
    hidePopup: PropTypes.func.isRequired,
    clipArticles: PropTypes.func.isRequired,
    articles: PropTypes.array.isRequired,
    recentClipFeeds: PropTypes.array.isRequired,
    getRecentClipFeeds: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  hidePopupFromOutside = (e) => {
    if (e.target === e.currentTarget) this.hidePopup()
  }

  hidePopup = () => {
    this.props.hidePopup()
  }

  onSubmit = () => {
    this.hidePopup()
  }

  componentWillMount = () => {
    this.props.getRecentClipFeeds()
  }

  onRecentFeedClick = (feed) => {
    this.props.clipArticles(feed.id)
    this.props.hidePopup()
  }

  render() {
    const { t, articles, recentClipFeeds } = this.props

    return (
      <Modal
        isOpen
        toggle={this.hidePopup}
        backdrop={false}
        modalClassName="pointer-events-none"
      >
        <ModalHeader toggle={this.hidePopup}>
          {t('searchTab.clipPopup.header')}
        </ModalHeader>
        <ModalBody>
          <div className="text-center">
            <p>{t('searchTab.clipPopup.hint1')}</p>

            <div className="draggable-container">
              <ClipDragSource articles={articles} />
            </div>

            {recentClipFeeds && recentClipFeeds.length > 0 && (
              <div className="mt-2">
                <p className="mb-2">{t('searchTab.clipPopup.hint2')}</p>
                <div className="d-flex justify-content-center flex-wrap">
                  {recentClipFeeds.map((feed) => {
                    return (
                      <RecentFeed
                        onRecentFeedClick={this.onRecentFeedClick}
                        key={feed.id}
                        feed={feed}
                      />
                    )
                  })}
                </div>
              </div>
            )}
          </div>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('common:commonWords.Cancel')}
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  ClipArticlesPopup
)
