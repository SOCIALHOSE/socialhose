import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import TimeAgo from 'timeago-react';
import ArticleComment from './ArticleComment';
import {
  UncontrolledDropdown,
  DropdownToggle,
  DropdownMenu,
  DropdownItem,
  CustomInput,
  Button
} from 'reactstrap';
import ShareMenu from './ShareMenu';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
  faFacebook,
  faInstagram,
  faPinterest,
  faReddit,
  faTumblr,
  faTwitter,
  faYoutube
} from '@fortawesome/free-brands-svg-icons';
import {
  faComments,
  faEye,
  faFrown,
  faMeh,
  faQuoteLeft,
  faShareAlt,
  faSmile,
  faThumbsDown,
  faThumbsUp
} from '@fortawesome/free-solid-svg-icons';
import {
  capOnlyFirstLetter,
  convertUTCtoLocal,
  abbreviateNumber,
  notNullAndUnd
} from '../../../../../common/helper';
import SourceIndexInfoPopup from '../SourceIndexSubTab/SourceIndexInfoPopup';

const icons = {
  twitter: faTwitter,
  facebook: faFacebook,
  instagram: faInstagram,
  tumblr: faTumblr,
  pinterest: faPinterest,
  reddit: faReddit,
  youtube: faYoutube,
  POSITIVE: faSmile,
  NEGATIVE: faFrown,
  NEUTRAL: faMeh
};

const colors = {
  POSITIVE: '#3ac47d',
  NEGATIVE: '#FC3939',
  NEUTRAL: '#868e96',
  twitter: '#1DA1F2',
  facebook: '#4267B2',
  reddit: '#FF5700',
  instagram: '#8a3ab9',
  tumblr: '#34526F',
  pinterest: '#E60023',
  youtube: '#FF0000'
};

export class Article extends React.Component {
  constructor() {
    super();
    this.state = {
      shareMenu: false,
      imgErr: false,
      sourceModal: false
    };

    this.elemDesc = React.createRef();
  }

  selectArticle = () => {
    this.props.selectArticle(this.props.article);
  };

  showEmailPopup = () => {
    this.props.showEmailPopup([this.props.article]);
  };

  showCommentPopup = () => {
    this.props.showCommentPopup(this.props.article);
  };

  showDeletePopup = () => {
    this.props.showDeletePopup([this.props.article]);
  };

  showClipPopup = () => {
    this.props.showClipPopup([this.props.article]);
  };

  toggleShareMenu = () => {
    this.setState((prev) => ({ shareMenu: !prev.shareMenu }));
  };

  loadMoreComments = () => {
    const {
      loadMoreComments,
      article: {
        id: articleId,
        comments: { count: offset }
      }
    } = this.props;
    loadMoreComments(articleId, offset);
  };

  readLater = () => {
    this.props.readArticleLater(this.props.article);
  };

  onImgError = () => {
    this.setState({ imgErr: true });
  };

  toggleSourceModal = () => {
    this.setState((prev) => ({ sourceModal: !prev.sourceModal }));
  };

  render() {
    const { article, t, i18n, showCommentPopup, deleteComment } = this.props;
    let {
      comments,
      id,
      source,
      sentiment,
      permalink,
      publisher,
      title,
      image,
      author,
      content,
      published,
      mentions,
      tags,
      likes,
      dislikes,
      views,
      shares,
      categories
    } = article;
    const { imgErr } = this.state;
    const {
      data: commentsData,
      count: commentsCount, // should get real post comment count
      totalCount: commentsTotalCount
    } = comments;

    const isArticleChosen = !!this.props.selectedArticles.find(
      (item) => item.id === id
    );

    const offsetWidth =
      this.elemDesc &&
      this.elemDesc.current &&
      this.elemDesc.current.offsetWidth;

    const hasRightCounters =
      notNullAndUnd(likes) ||
      notNullAndUnd(dislikes) ||
      commentsCount || // add not null and undefined when counter shows
      notNullAndUnd(views) ||
      notNullAndUnd(shares) ||
      notNullAndUnd(mentions);

    const isTwitter = source.siteType === 'twitter';
    const isInstagram = source.siteType === 'instagram';
    let username;
    if (isTwitter) {
      username =
        author.link &&
        author.link.match(
          /^https?:\/\/(www\.)?twitter\.com\/(#!\/)?([^\/]+)(\/\w+)*$/
        );
      username = username && username[3];
    }
    if (isInstagram) {
      username =
        author.link &&
        author.link.match(
          /(?:(?:http|https):\/\/)?(?:www\.)?(?:instagram\.com|instagr\.am)\/([A-Za-z0-9-_\.]+)/
        );
      username = username && username[1];
    }

    const isRTL = document.documentElement.dir === 'rtl';
    return (
      <div className="post border b-radius-5 mb-4">
        <UncontrolledDropdown className="post__menu">
          <DropdownToggle
            outline
            color="primary"
            className="btn-icon btn-icon-only p-1 m-2"
          >
            <i className="lnr lnr-menu btn-icon-wrapper" />
          </DropdownToggle>
          <DropdownMenu className={isRTL ? ' dropdown-menu-left' : ''}>
            <DropdownItem
              className="text-muted"
              onClick={this.showCommentPopup}
            >
              <i className="mr-2 fa fa-comments"> </i>
              <span>{t('searchTab.commentBtn')}</span>
            </DropdownItem>
            <DropdownItem className="text-muted" onClick={this.showClipPopup}>
              <i className="mr-2 fa fa-cut"> </i>
              <span>{t('searchTab.clipBtn')}</span>
            </DropdownItem>
            <DropdownItem className="text-muted" onClick={this.readLater}>
              <i className="mr-2 fa fa-bookmark"> </i>
              <span>{t('searchTab.readLaterBtn')}</span>
            </DropdownItem>
            <DropdownItem className="text-muted" onClick={this.readLater}>
              <i className="mr-2 fa fa-archive"> </i>
              <span>{t('searchTab.archiveBtn')}</span>
            </DropdownItem>
            <DropdownItem className="text-muted" onClick={this.showEmailPopup}>
              <i className="mr-2 fa fa-envelope"> </i>
              <span>{t('searchTab.emailBtn')}</span>
            </DropdownItem>
            <DropdownItem className="text-muted" onClick={this.toggleShareMenu}>
              <i className="mr-2 fa fa-share-alt"> </i>
              <span>{t('searchTab.shareBtn')}</span>
            </DropdownItem>
            <DropdownItem className="text-muted" onClick={this.showDeletePopup}>
              <i className="mr-2 fa fa-trash"> </i>
              <span>{t('searchTab.deleteBtn')}</span>
            </DropdownItem>
          </DropdownMenu>
        </UncontrolledDropdown>
        <div className="d-flex flex-row">
          <div className="post__icons">
            <CustomInput
              id={'article-check-' + id}
              type="checkbox"
              className="mb-3"
              onChange={this.selectArticle}
              checked={isArticleChosen}
            />
            {source.siteType && (
              <FontAwesomeIcon
                title={capOnlyFirstLetter(source.siteType)}
                icon={icons[source.siteType]}
                size="lg"
                className="fa-w-16 mb-3"
                color={colors[source.siteType]}
              />
            )}
            {sentiment && (
              <FontAwesomeIcon
                title={capOnlyFirstLetter(sentiment)}
                icon={icons[sentiment]}
                className="mb-3"
                size="lg"
                color={colors[sentiment]}
              />
            )}
          </div>
          <div className="post_middlepart">
            <h2 className="post__title">
              {title && (
                <a href={permalink} target="_blank" rel="noopener noreferrer">
                  {title}
                </a>
              )}
            </h2>
            <div
              ref={this.elemDesc}
              className={`post__content${
                offsetWidth && offsetWidth < 430 ? ' flex-column' : ''
              }`}
            >
              {image &&
                !imgErr &&
                (!title && permalink ? (
                  <a href={permalink} target="_blank" rel="noopener noreferrer">
                    <img
                      id={id}
                      width="180px"
                      className="post__img mb-2 mb-lg-0 mr-3"
                      src={image}
                      onError={this.onImgError}
                    />
                  </a>
                ) : (
                  <img
                    id={id}
                    width="180px"
                    className="post__img mb-2 mb-lg-0 mr-3"
                    src={image}
                    onError={this.onImgError}
                  />
                ))}

              <div>
                {author.name ? (
                  author.link ? (
                    <a
                      className="d-inline-block hover-link text-muted mb-2"
                      href={author.link}
                      target="_blank"
                    >
                      {username ? `@${username}` : author.name}
                    </a>
                  ) : (
                    <p className="text-muted mb-2">{author.name}</p>
                  )
                ) : null}
                {!title && permalink ? (
                  <a
                    href={permalink}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="post__desc-link"
                  >
                    <p
                      className="post__desc"
                      dangerouslySetInnerHTML={{ __html: content }}
                    ></p>
                  </a>
                ) : (
                  <p
                    className="post__desc"
                    dangerouslySetInnerHTML={{ __html: content }}
                  ></p>
                )}
              </div>
            </div>

            {tags && tags.length && tags.length > 0 && (
              <div className="post__tags mt-2">
                <strong>{t('searchTab.tags')}</strong>: {tags.join(', ')}
              </div>
            )}

            {categories && categories.length > 0 && (
              <p className="post__tags my-2">
                <strong>{t('searchTab.categories')}</strong>:{' '}
                {categories.join(', ')}
              </p>
            )}
            <div className="post__about-info text-muted mt-3">
              {published && (
                <Fragment>
                  <span
                    className="d-inline-block"
                    title={convertUTCtoLocal(published, 'MM/DD/YYYY HH:mm:ss')}
                  >
                    <TimeAgo
                      datetime={published}
                      locale={i18n.language}
                      opts={{ minInterval: 60 }}
                    />
                  </span>
                  <span className="mx-2">|</span>
                </Fragment>
              )}

              {source.type && (
                <Fragment>
                  <span>{capOnlyFirstLetter(source.type)}</span>
                  <span className="mx-2">|</span>
                </Fragment>
              )}

              {source.country && (
                <Fragment>
                  <span>{source.country}</span>
                  <span className="mx-2">|</span>
                </Fragment>
              )}

              {publisher && (
                <Fragment>
                  <Button
                    color="link"
                    className="btn-anchor"
                    title="Click to see details"
                    onClick={this.toggleSourceModal}
                  >
                    {publisher}
                  </Button>
                  <span className="mx-2">|</span>
                </Fragment>
              )}

              {source.title && (
                <Fragment>
                  {publisher ? (
                    <a
                      href={source.link}
                      style={{ overflowWrap: 'anywhere' }}
                      rel="noopener noreferrer"
                      target="_blank"
                    >
                      {source.title}
                    </a>
                  ) : (
                    <Button
                      color="link"
                      className="btn-anchor"
                      title="Click to see details"
                      onClick={this.toggleSourceModal}
                    >
                      {(isTwitter || isInstagram) && author.name
                        ? author.name
                        : source.title}
                    </Button>
                  )}
                </Fragment>
              )}
            </div>
          </div>
          {hasRightCounters && (
            <div className="post__extras p-3">
              <div className="post__icons-wrapper">
                {notNullAndUnd(likes) && (
                  <div className="post__icon-metrics mb-1">
                    <FontAwesomeIcon
                      title="Likes"
                      icon={faThumbsUp}
                      className="text-success"
                    />
                    <p className="ml-2" title={likes}>
                      {abbreviateNumber(likes)}
                    </p>
                  </div>
                )}
                {notNullAndUnd(dislikes) && (
                  <div className="post__icon-metrics mb-1">
                    <FontAwesomeIcon title="Dislikes" icon={faThumbsDown} />
                    <p className="ml-2" title={dislikes}>
                      {abbreviateNumber(dislikes)}
                    </p>
                  </div>
                )}
                {/* {notNullAndUnd(commentsCount) && ( 
                Add above line when real comment counts are visible
                */}
                {commentsCount ? (
                  <div className="post__icon-metrics mb-1">
                    <FontAwesomeIcon title="Comments" icon={faComments} />
                    <p className="ml-2" title={commentsCount}>
                      {abbreviateNumber(commentsCount)}
                    </p>
                  </div>
                ) : (
                  ''
                )}
                {notNullAndUnd(views) && (
                  <div className="post__icon-metrics mb-1">
                    <FontAwesomeIcon title="Viwes" icon={faEye} />
                    <p className="ml-2 text-center" title={views}>
                      {abbreviateNumber(views)}
                    </p>
                  </div>
                )}
                {notNullAndUnd(shares) && (
                  <div className="post__icon-metrics mb-1">
                    <FontAwesomeIcon title="Shares" icon={faShareAlt} />
                    <p className="ml-2 text-center" title={shares}>
                      {abbreviateNumber(shares)}
                    </p>
                  </div>
                )}
                {notNullAndUnd(mentions) && (
                  <div className="post__icon-metrics mb-1">
                    <FontAwesomeIcon title="Mentions" icon={faQuoteLeft} />
                    <p className="ml-2 text-center" title={mentions}>
                      {abbreviateNumber(mentions)}
                    </p>
                  </div>
                )}
              </div>
            </div>
          )}
        </div>

        {commentsData && commentsData.length > 0 && (
          <div className="post__comments border-top px-3 pb-3">
            {commentsData.map((comment) => {
              return (
                <ArticleComment
                  article={article}
                  comment={comment}
                  showCommentPopup={showCommentPopup}
                  deleteComment={deleteComment}
                  key={comment.id}
                />
              );
            })}

            {commentsCount < commentsTotalCount && (
              <Button
                outline
                size="sm"
                color="light"
                className="mt-2 d-block ml-auto btn-icon"
                onClick={this.loadMoreComments}
              >
                <i className="lnr lnr-chevron-down btn-icon-wrapper" />{' '}
                {t('searchTab.moreComments')}
              </Button>
            )}
          </div>
        )}

        {this.state.shareMenu && (
          <ShareMenu article={article} hideMenu={this.toggleShareMenu} />
        )}

        {this.state.sourceModal && (
          <SourceIndexInfoPopup
            source={article.source}
            hideSourceInfoPopup={this.toggleSourceModal}
          />
        )}
      </div>
    );
  }
}

Article.propTypes = {
  article: PropTypes.object.isRequired,
  selectedArticles: PropTypes.array.isRequired,
  selectArticle: PropTypes.func.isRequired,
  showEmailPopup: PropTypes.func.isRequired,
  showDeletePopup: PropTypes.func.isRequired,
  showCommentPopup: PropTypes.func.isRequired,
  showClipPopup: PropTypes.func.isRequired,
  deleteComment: PropTypes.func.isRequired,
  readArticleLater: PropTypes.func.isRequired,
  loadMoreComments: PropTypes.func.isRequired,
  showShareMenu: PropTypes.func.isRequired,
  i18n: PropTypes.object.isRequired,
  t: PropTypes.func.isRequired
};

export default translate(['tabsContent'], { wait: true })(Article);
