import React from 'react'
import PropTypes from 'prop-types'
import classnames from 'classnames'

export class Filter extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    areFeedsFiltered: PropTypes.bool.isRequired,
    categories: PropTypes.array.isRequired,
    setFilteredCategories: PropTypes.func.isRequired,
    clearFilteredCategories: PropTypes.func.isRequired
  }

  constructor (props) {
    super(props)
    this.state = {
      sidebarAnimationDisabled: true,
      activeSearch: false
    }
  }

  activeSearchFunc = () => {
    this.setState({ activeSearch: !this.state.activeSearch })
    this.clearFilter()
  }

  filterCategoriesList = (
    categories,
    searchQuery,
    setParentBranchMatchFromParent
  ) => {
    // show category if there is feed
    return categories.filter((category) => {
      category.branchMatch = false

      //function that sets parent branchMatch prop
      function setParentBranchMatch (flag) {
        category.branchMatch = flag
      }

      if (category.childes.length > 0) {
        category.childes = this.filterCategoriesList(
          category.childes,
          searchQuery,
          setParentBranchMatch
        )
      }

      // filter feeds in category
      category.feeds = category.feeds.filter((feed) => {
        return feed.name.toLowerCase().indexOf(searchQuery) !== -1
      })
      // if this category is a child and it has matched feeds or its child have, then we set branchMatch prop of parent
      if (
        (category.feeds.length > 0 && setParentBranchMatchFromParent) ||
        (category.branchMatch && setParentBranchMatchFromParent)
      ) {
        setParentBranchMatchFromParent(true)
      }

      return category.branchMatch || category.feeds.length > 0
    })
  }

  filterSidebarItems = (e) => {
    const searchQuery = e.target.value.toLowerCase()
    const categoriesCopy = this.props.categories.slice(0)

    if (searchQuery.length) {
      const filteredCat = this.filterCategoriesList(categoriesCopy, searchQuery)
      this.props.setFilteredCategories(filteredCat)
    } else {
      this.props.clearFilteredCategories()
    }
  }

  clearFilter = () => {
    this.props.clearFilteredCategories()
  }

  render () {
    return (
      <div
        className={classnames('search-wrapper mb-1', {
          active: this.state.activeSearch
        })}
      >
        <div className="input-holder">
          <input
            type="text"
            className="search-input"
            placeholder={this.props.t('common:sidebar.typeToSearch')}
            onKeyUp={this.filterSidebarItems}
            id="sidebar-search"
          />
          <button onClick={this.activeSearchFunc} className="search-icon">
            <span />
          </button>
        </div>
        <button onClick={this.activeSearchFunc} className="close"></button>
      </div>
    )
  }
}

export default Filter
