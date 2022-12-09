import React from 'react';
import PropTypes from 'prop-types';
import { DropTarget, DragSource } from 'react-dnd';
import { compose } from 'redux';
import onClickOutside from 'react-onclickoutside';
import Feed from './Feed';
import CategoryHead from './CategoryHead';
import { TYPES } from '../../../redux/modules/appState/sidebar';
import cx from 'classnames';

const folderSource = {
  beginDrag(props) {
    return {
      type: TYPES.FOLDER,
      id: props.category.id,
      category: props.category
    };
  },

  canDrag(props) {
    return props.category.type === 'directory';
  }
};

const targetTypes = [TYPES.FEED, TYPES.FOLDER];
const categoryTarget = {
  drop(props, monitor) {
    if (monitor.didDrop()) return;
    const { category, moveCategory, moveFeed } = props;

    const item = monitor.getItem();
    const draggedCategoryId = item.id;
    const newCategoryId = category.id;

    if (item.type === TYPES.FOLDER) {
      moveCategory(item.category, newCategoryId);
    } else if (item.type === TYPES.FEED) {
      moveFeed(draggedCategoryId, newCategoryId);
    }
  },

  canDrop(props, monitor) {
    const categoryType = props.category.type;
    return (
      categoryType !== 'deleted_content' && categoryType !== 'shared_content'
    );
  }
};

export class CategoryClass extends React.Component {
  static propTypes = {
    parentId: PropTypes.number.isRequired,
    category: PropTypes.object.isRequired,
    showDeletePopup: PropTypes.func.isRequired,
    showRenamePopup: PropTypes.func.isRequired,
    showAddCategoryPopup: PropTypes.func.isRequired,
    showAddClippingsFeedPopup: PropTypes.func.isRequired,
    hideParentCategoryDrop: PropTypes.func.isRequired,
    categories: PropTypes.array.isRequired,
    connectDropTarget: PropTypes.func.isRequired,
    connectDragSource: PropTypes.func.isRequired,
    getFeedResults: PropTypes.func.isRequired,
    moveFeed: PropTypes.func.isRequired,
    moveCategory: PropTypes.func.isRequired,
    clipArticles: PropTypes.func.isRequired,
    toggleExportFeed: PropTypes.func.isRequired,
    toggleExportCategory: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.state = {
      isCategoryActive: true, // sub menus
      isCategoryDropActive: false // more options
    };
  }

  // hide category dropdown if there was click outside
  handleClickOutside = () => {
    this.state.isCategoryDropActive && this.hideCategoryDropdown();
  };

  toggleCollapse = (e) => {
    if (e.target === e.currentTarget) {
      this.setState((prev) => ({
        isCategoryActive: !prev.isCategoryActive
      }));
    }
  };

  toggleCategoryDropdown = (e) => {
    e.preventDefault();
    // this.props.hideParentCategoryDrop();
    this.setState((prev) => ({
      isCategoryDropActive: !prev.isCategoryDropActive
    }));
  };

  hideCategoryDropdown = () => {
    this.setState({
      isCategoryDropActive: false
    });
  };

  render() {
    const {
      category,
      categories,
      connectDropTarget,
      connectDragSource,
      hideParentCategoryDrop,
      parentId,
      showDeletePopup,
      getFeedResults,
      showRenamePopup,
      showAddCategoryPopup,
      moveCategory,
      moveFeed,
      showAddClippingsFeedPopup,
      clipArticles,
      toggleExportFeed,
      toggleExportCategory
    } = this.props;

    const isFeeds = category.feeds.length > 0;
    const isChildes = category.childes.length > 0;
    const categoryType = category.type;

    let categoryActiveClass = this.state.isCategoryActive
      ? ' active-category'
      : '';

    return connectDragSource(
      connectDropTarget(
        <li
          className={'metismenu-item ' + categoryType + categoryActiveClass}
          onClick={hideParentCategoryDrop}
        >
          <CategoryHead
            toggleCollapse={this.toggleCollapse}
            toggleCategoryDropdown={this.toggleCategoryDropdown}
            isCategoryDropActive={this.state.isCategoryDropActive}
            isCategoryActive={this.state.isCategoryActive}
            hideDropDown={this.hideCategoryDropdown}
            parentId={parentId}
            category={category}
            showDeletePopup={showDeletePopup}
            showRenamePopup={showRenamePopup}
            showAddCategoryPopup={showAddCategoryPopup}
            toggleExportCategory={toggleExportCategory}
            showAddClippingsFeedPopup={showAddClippingsFeedPopup}
            categories={categories}
          />

          <ul
            className={cx('metismenu-container', {
              visible: this.state.isCategoryActive
            })}
          >
            {isFeeds &&
              category.feeds.map((feed, i) => {
                return (
                  <Feed
                    key={'feed' + i}
                    feed={feed}
                    showDeletePopup={showDeletePopup}
                    showRenamePopup={showRenamePopup}
                    categories={categories}
                    categoryId={category.id}
                    hideParentCategoryDrop={this.hideCategoryDropdown}
                    getFeedResults={getFeedResults}
                    clipArticles={clipArticles}
                    toggleExportFeed={toggleExportFeed}
                  />
                );
              })}

            {isChildes &&
              category.childes.map((_category, i) => {
                return (
                  <Category
                    key={'category' + i}
                    showDeletePopup={showDeletePopup}
                    showRenamePopup={showRenamePopup}
                    showAddCategoryPopup={showAddCategoryPopup}
                    showAddClippingsFeedPopup={showAddClippingsFeedPopup}
                    parentId={category.id}
                    category={_category}
                    categories={categories}
                    hideParentCategoryDrop={this.hideCategoryDropdown}
                    getFeedResults={getFeedResults}
                    connectDropTarget={connectDropTarget}
                    connectDragSource={connectDragSource}
                    moveCategory={moveCategory}
                    moveFeed={moveFeed}
                    clipArticles={clipArticles}
                    toggleExportFeed={toggleExportFeed}
                    toggleExportCategory={toggleExportCategory}
                  />
                );
              })}
          </ul>
        </li>
      )
    );
  }
}

export const Category = compose(
  DropTarget(targetTypes, categoryTarget, (connect, monitor) => ({
    connectDropTarget: connect.dropTarget(),
    itemType: monitor.getItemType()
  })),
  DragSource(TYPES.FOLDER, folderSource, (connect) => ({
    connectDragSource: connect.dragSource()
  }))
)(onClickOutside(CategoryClass));

export default Category;
