import React from 'react'
import PropTypes from 'prop-types'

export class SourceIcon extends React.Component {
  static propTypes = {
    type: PropTypes.string.isRequired
  };

  acceptedTypes = ['blogs', 'clippings', 'forums', 'mixed', 'news', 'prints', 'socials', 'user-added', 'user-comments', 'videos'];

  render () {
    const { type } = this.props

    if (!this.acceptedTypes.includes(type)) {
      return null
    }

    return (
      <img src={require('../../../../../../images/feed-type-' + type + '.png')} className="source-icon" />
    )
  }

}

export default SourceIcon
