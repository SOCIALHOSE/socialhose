import React from 'react'
import PropTypes from 'prop-types'
import { Button } from 'reactstrap'

export default class LimitSelector extends React.Component {

  static propTypes = {
    pagerAction: PropTypes.func.isRequired,
    limit: PropTypes.number.isRequired,
    isCurrent: PropTypes.bool.isRequired
  };

  onClick = () => {
    !this.props.isCurrent && this.props.pagerAction({limitByPage: this.props.limit})
  };

  render () {
    let className = 'table-pager__limit'
    if (this.props.isCurrent) {
      className += ' ' + className + '--current'
    }

    return (
      <Button color="primary" className={className} onClick={this.onClick}>
        {this.props.limit}
      </Button>
    )
  }

}
