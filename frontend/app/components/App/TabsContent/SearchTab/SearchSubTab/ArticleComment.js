import React from 'react'
import PropTypes from 'prop-types'
import { translate, Interpolate } from 'react-i18next'
import TimeAgo from 'timeago-react'
import { Button } from 'reactstrap'

export class ArticleComment extends React.Component {
  static propTypes = {
    article: PropTypes.object.isRequired,
    comment: PropTypes.func.isRequired,
    deleteComment: PropTypes.func.isRequired,
    showCommentPopup: PropTypes.func.isRequired,
    i18n: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  }

  onEdit = () => {
    const { showCommentPopup, article, comment } = this.props
    showCommentPopup(article, comment)
  }

  onDelete = () => {
    const { deleteComment, article, comment } = this.props
    deleteComment(comment.id, article.id)
  }

  render() {
    const { comment, i18n } = this.props

    return (
      <div className="post__comment mt-2">
        <div className="d-flex justify-content-between">
          <div>
            <cite className="post__commentor mr-3">
              <Interpolate
                i18nKey="searchTab.commentMetadata"
                author={`${comment.author.firstName} ${comment.author.lastName}`}
              />
            </cite>
            <span className="post__cmttime mr-3 text-muted">
              <TimeAgo
                datetime={comment.createdAt}
                locale={i18n.language}
                opts={{ minInterval: 30 }}
              />
            </span>
          </div>
          <div>
            <Button color="link" className="p-0" onClick={this.onEdit}>
              <i className="lnr lnr-pencil"></i>
            </Button>
            <Button color="link" className="ml-2 p-0" onClick={this.onDelete}>
              <i className="lnr lnr-trash"></i>
            </Button>
          </div>
        </div>
        <p className="post__cmt-content">
          <strong className="d-block mb-1">{comment.title}</strong>
          {comment.content}
        </p>
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(ArticleComment)
