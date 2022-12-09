import React from 'react';
import PropTypes from 'prop-types';
import { Interpolate, translate } from 'react-i18next';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';

export class DeleteArticlesPopup extends React.Component {
  static propTypes = {
    articles: PropTypes.array.isRequired,
    activeFeed: PropTypes.object,
    hidePopup: PropTypes.func.isRequired,
    deleteArticles: PropTypes.func.isRequired,
    deleteArticlesFromFeed: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  onSubmit = () => {
    const {
      articles,
      activeFeed,
      deleteArticles,
      deleteArticlesFromFeed,
      hidePopup
    } = this.props;
    const ids = articles.map((a) => a.id);
    if (activeFeed) {
      deleteArticlesFromFeed(ids, activeFeed.id);
    } else {
      deleteArticles(ids);
    }
    hidePopup();
  };

  render() {
    const { t, articles, hidePopup } = this.props;

    return (
      <Modal isOpen toggle={hidePopup} backdrop="static">
        <ModalHeader toggle={hidePopup}>{t('commonWords.Confirm')}</ModalHeader>
        <ModalBody>
          <p>
            {articles.length > 1 ? (
              <Interpolate
                t={t}
                i18nKey="tabsContent:searchTab.deleteArticlePopupText_plural"
                articlesLength={articles.length}
              />
            ) : (
              t('tabsContent:searchTab.deleteArticlePopupText')
            )}
          </p>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={hidePopup}>
            {t('commonWords.Cancel')}
          </Button>
          <Button color="danger" onClick={this.onSubmit}>
            {t('commonWords.Delete')}
          </Button>
        </ModalFooter>
      </Modal>
    );
  }
}

export default translate(['common'], { wait: true })(DeleteArticlesPopup);
