import React from 'react'
import PropTypes from 'prop-types'
import {translate} from 'react-i18next'
import onClickOutside from 'react-onclickoutside'
import {compose} from 'redux'

class ShareMenu extends React.Component {

  static propTypes = {
    article: PropTypes.object.isRequired,
    hideMenu: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  handleClickOutside = () => {
    this.props.hideMenu()
  };

  _winOpen = (url) => {
    window.open(url, 'share', 'width=600, height=450, top=0, left=0, toolbar=no')
  };

  onTweet = () => {
    this._winOpen('https://twitter.com/intent/tweet?url=' + this.props.article.source.link)
    this.props.hideMenu()
  };

  onYammer = () => {
    this._winOpen('https://www.yammer.com/')
    this.props.hideMenu()
  };

  render () {
    const { t } = this.props

    return (
      <div className="article-share-menu">
        <a onClick={this.onTweet}>{t('searchTab.tweet')}</a>
        <a onClick={this.onYammer}>{t('searchTab.yammer')}</a>
      </div>
    )
  }
}

const applyDecorators = compose(
  translate(['tabsContent'], {wait: true}),
  onClickOutside
)

export default applyDecorators(ShareMenu)
