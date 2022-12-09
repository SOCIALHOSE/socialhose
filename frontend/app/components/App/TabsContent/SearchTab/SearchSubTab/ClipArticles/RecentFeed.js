import React from 'react'
import PropTypes from 'prop-types'
import { Button } from 'reactstrap'

export default class RecentFeed extends React.Component {

  static propTypes = {
    feed: PropTypes.object.isRequired,
    onRecentFeedClick: PropTypes.func.isRequired
  };

  onClick = () => {
    this.props.onRecentFeedClick(this.props.feed)
  }

  render () {
    const { feed } = this.props

    return (
      <Button color="light" className={'mr-2 mb-2 feed-icon ' + feed.class} onClick={this.onClick}>
        {feed.name}
      </Button>
    )
  }

}
