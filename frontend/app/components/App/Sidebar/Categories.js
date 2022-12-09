import React from 'react'
import PropTypes from 'prop-types'
import Category from './Category'

export class Categories extends React.Component {
  static propTypes = {
    actions: PropTypes.object.isRequired,
    areCategoriesLoaded: PropTypes.bool.isRequired,
    areFeedsFiltered: PropTypes.bool.isRequired,
    categories: PropTypes.array.isRequired,
    filteredCategories: PropTypes.array.isRequired
  };

  hideParentCategoryDrop = () => {}; //empty func for first level categories

  render () {
    const { areCategoriesLoaded, areFeedsFiltered, actions } = this.props
    const {
      showDeletePopup, showRenamePopup, showAddCategoryPopup,
      showAddClippingsFeedPopup, getFeedResults,
      moveCategory, moveFeed, toggleExportFeed,
      toggleExportCategory, clipArticles
    } = actions

    const categories = areFeedsFiltered ? this.props.filteredCategories : this.props.categories

    return (
      <div className='sidebar-categories'>

        {areCategoriesLoaded &&
          categories.map((category, i) => {
            return (
              <Category
                hideParentCategoryDrop={this.hideParentCategoryDrop} //set empty func
                parentId={-1} //set empty parent category for first level categories
                category={category}
                categories={categories}
                showDeletePopup={showDeletePopup}
                showRenamePopup={showRenamePopup}
                showAddCategoryPopup={showAddCategoryPopup}
                showAddClippingsFeedPopup={showAddClippingsFeedPopup}
                getFeedResults={getFeedResults}
                moveCategory={moveCategory}
                moveFeed={moveFeed}
                clipArticles={clipArticles}
                key={'main-category' + i}
                toggleExportFeed={toggleExportFeed}
                toggleExportCategory={toggleExportCategory}
              />
            )
          })
        }
      </div>
    )
  }
}

export default Categories
