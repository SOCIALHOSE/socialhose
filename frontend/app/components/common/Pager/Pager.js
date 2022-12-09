import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import LimitSelector from './LimitSelector'
import PageSelector from './PageSelector'
import { ButtonGroup, Pagination, PaginationItem, PaginationLink } from 'reactstrap'

export class Pager extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    pagerAction: PropTypes.func.isRequired,
    currentPage: PropTypes.number.isRequired,
    numPages: PropTypes.number.isRequired,
    limitByPage: PropTypes.number.isRequired,
    hideLimitSelector: PropTypes.bool
  };

  static limits = [10, 25, 50, 100, 200];

  onClickPrevPage = () => {
    if (this.props.currentPage > 1) {
      this.props.pagerAction({ currentPage: this.props.currentPage - 1 })
    }
  };

  onClickNextPage = () => {
    if (this.props.currentPage < this.props.numPages) {
      this.props.pagerAction({ currentPage: this.props.currentPage + 1 })
    }
  };

  getPaginationTemplate = (maxLength = 7) => {
    const { numPages, currentPage } = this.props
    let res = {}
    if (numPages === 0) return res

    //always show first, last and current page
    res[1] = 1
    res[numPages] = 1
    res[currentPage] = 1

    if (currentPage <= maxLength - 3) {
      //show all from 1 to 5
      for (let i = 2; i < maxLength - 1; i++) {
        if (i < numPages) res[i] = 1
      }
    } else if (currentPage >= numPages - maxLength + 4) {
      //show last five pages
      for (let i = numPages - maxLength + 3; i < numPages; i++) {
        res[i] = 1
      }
    } else {
      //just show neighbours of current page
      let shift = Math.floor((maxLength - 5) / 2)
      for (let i = currentPage - shift; i <= currentPage + shift; i++) {
        res[i] = 1
      }
    }
    //and show ellipsis
    if (numPages > 1) {
      if (!res[2]) res[2] = 0
      if (!res[numPages - 1]) res[numPages - 1] = 0
    }
    return res
  };

  render () {
    const pages = this.getPaginationTemplate()
    // const prevDisabledClass =
    //   this.props.currentPage > 1 ? '' : ' table-pager__page--disabled'
    // const nextDisabledClass =
    //   this.props.currentPage < this.props.numPages
    //     ? ''
    //     : ' table-pager__page--disabled'

    return (
      <div className="table-pager">
        <Pagination
          className="pagination-rounded"
          aria-label="Page navigation example"
        >
          <PaginationItem>
            <PaginationLink onClick={this.onClickPrevPage} previous />
          </PaginationItem>

          {Object.keys(pages).map((index) => {
            return (
              <PageSelector
                pagerAction={this.props.pagerAction}
                key={index}
                index={parseInt(index)}
                isEllipsis={pages[index] === 0}
                isCurrent={parseInt(index) === this.props.currentPage}
              />
            )
          })}

          <PaginationItem>
            <PaginationLink onClick={this.onClickNextPage} next />
          </PaginationItem>
        </Pagination>

        {!this.props.hideLimitSelector && (
          <div className="table-pager__limits">
            <span>Show</span>
            <ButtonGroup>
              {Pager.limits.map((val) => {
                return (
                  <LimitSelector
                    pagerAction={this.props.pagerAction}
                    key={val}
                    limit={val}
                    isCurrent={val === this.props.limitByPage}
                  />
                )
              })}
            </ButtonGroup>
          </div>
        )}
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(Pager)
