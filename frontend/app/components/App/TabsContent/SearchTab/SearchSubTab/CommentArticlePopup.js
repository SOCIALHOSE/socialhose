import React from 'react';
import PropTypes from 'prop-types';
import { translate, Interpolate } from 'react-i18next';
import TimeAgo from 'timeago-react';
import {
  Button,
  Input,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader
} from 'reactstrap';

const initCharactersCount = 5000;

export class CommentArticlePopup extends React.Component {
  static propTypes = {
    article: PropTypes.object.isRequired,
    comment: PropTypes.object,
    commentArticle: PropTypes.func.isRequired,
    updateComment: PropTypes.func.isRequired,
    hidePopup: PropTypes.func.isRequired,
    i18n: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    const content = props.comment ? props.comment.content : '';
    this.state = {
      charactersCount: initCharactersCount - content.length,
      title: props.comment ? props.comment.title : '',
      comment: content
    };
  }

  handleTitleChange = (e) => {
    const { value } = e.target;
    this.setState({ title: value });
  };

  hidePopup = () => {
    this.props.hidePopup();
  };

  onSubmit = () => {
    const newComment = {
      title: this.state.title,
      content: this.state.comment
    };
    if (this.props.comment) {
      //edit exisitng
      this.props.updateComment(newComment, this.props.article.id);
    } else {
      //create new comment
      this.props.commentArticle(newComment, this.props.article.id);
    }
    this.hidePopup();
  };

  onChangeComment = (e) => {
    const charactersCount = initCharactersCount - e.target.value.length;

    if (charactersCount >= 0) {
      this.setState({
        charactersCount: charactersCount,
        comment: e.target.value
      });
    }
  };

  render() {
    const { t, i18n, article, comment } = this.props;
    const popupTitle = comment
      ? t('searchTab.commentPopup.editUserComment')
      : t('searchTab.commentPopup.addUserComment');

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>{popupTitle}</ModalHeader>
        <ModalBody>
          <div className="mb-3">
            <a
              className="font-size-lg"
              href={article.permalink}
              target="_blank"
              rel="noopener noreferrer"
            >
              {article.title}
            </a>
            <p>{article.author.name}</p>
            <p className="font-size-xs text-muted">
              <TimeAgo
                datetime={article.published}
                locale={i18n.language}
                opts={{ minInterval: 30 }}
              />
            </p>
          </div>

          <Input
            value={this.state.title}
            type="text"
            className="mb-2"
            onChange={this.handleTitleChange}
            placeholder={t('searchTab.commentPopup.inputTitlePlaceholder')}
          />

          <Input
            rows="3"
            type="textarea"
            value={this.state.comment}
            onChange={this.onChangeComment}
            placeholder={t('searchTab.commentPopup.commentPlanceholder')}
          />

          <p className="font-size-xs text-muted text-right mt-1">
            <Interpolate
              i18nKey="searchTab.commentPopup.charactersLeft"
              count={this.state.charactersCount}
            />
          </p>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('common:commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('common:commonWords.submit')}
          </Button>
        </ModalFooter>
      </Modal>
    );
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  CommentArticlePopup
);
