import React from 'react'
import PropTypes from 'prop-types'
import { PaginationItem, PaginationLink } from 'reactstrap'

export default class PageSelector extends React.Component {
  static propTypes = {
    pagerAction: PropTypes.func.isRequired,
    index: PropTypes.number.isRequired,
    isEllipsis: PropTypes.bool.isRequired,
    isCurrent: PropTypes.bool.isRequired
  };

  onClick = () => {
    !this.props.isCurrent &&
      this.props.pagerAction({ currentPage: this.props.index })
  };

  render () {
    if (this.props.isEllipsis) {
      return <span className="table-pager__ellipsis">. . .</span>
    } else {
      // const currentClass = this.props.isCurrent
      //   ? ' table-pager__page--current'
      //   : ''
      return (
        <PaginationItem active={this.props.isCurrent}>
          <PaginationLink onClick={this.onClick}>
            {this.props.index}
          </PaginationLink>
        </PaginationItem>
      )
    }
  }
}
