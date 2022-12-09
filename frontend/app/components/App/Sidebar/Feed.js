/** DRAG SOURCE **/
import React from 'react';
import PropTypes from 'prop-types';
import { compose } from 'redux';
import SidebarDropdown from './SidebarDropdown';
import { DragSource, DropTarget } from 'react-dnd';
import onClickOutside from 'react-onclickoutside';
import { TYPES } from '../../../redux/modules/appState/sidebar';
import { withRouter } from 'react-router-dom';

const feedSource = {
  beginDrag(props) {
    return {
      type: TYPES.FEED,
      id: props.feed.id,
      feed: props.feed,
      currentCategoryId: props.categoryId
    };
  }
};
/**
 * Specifies which props to inject into component from Drag n Drop.
 */
function dragCollect(connect) {
  return {
    // Call this function inside render()
    // to let React DnD handle the drag events:
    connectDragSource: connect.dragSource()
  };
}

/** DROP TARGET **/
const feedTarget = {
  drop(props, monitor) {
    if (monitor.didDrop()) return;
    const { feed, clipArticles } = props;
    clipArticles(feed.id);
  },

  canDrop(props, monitor) {
    return props.feed.subType === 'clip_feed';
  }
};

function dropCollect(connect, monitor) {
  return {
    connectDropTarget: connect.dropTarget()
  };
}

export class Feed extends React.Component {
  static propTypes = {
    feed: PropTypes.object.isRequired,
    categoryId: PropTypes.number.isRequired,
    categories: PropTypes.array.isRequired,
    showDeletePopup: PropTypes.func.isRequired,
    showRenamePopup: PropTypes.func.isRequired,
    hideParentCategoryDrop: PropTypes.func.isRequired,
    connectDragSource: PropTypes.func.isRequired,
    connectDropTarget: PropTypes.func.isRequired,
    getFeedResults: PropTypes.func.isRequired,
    clipArticles: PropTypes.func.isRequired,
    toggleExportFeed: PropTypes.func.isRequired,
    history: PropTypes.object.isRequired
  };

  constructor(props) {
    super(props);
    this.state = {
      isItemDropActive: false
    };
  }

  //hide feed dropdown if there was click outside
  handleClickOutside = () => {
    this.state.isItemDropActive && this.hideDropDown();
  };

  hideDropDown = () => {
    this.setState({
      isItemDropActive: false
    });
  };

  toggleItemDropdown = (e) => {
    e.preventDefault();
    this.setState({
      isItemDropActive: !this.state.isItemDropActive
    });
  };

  onFeedClick = (e) => {
    const { history, getFeedResults, feed } = this.props;
    e.preventDefault();
    history.push('/app/search/search');
    getFeedResults({ page: 1 }, feed.id);
    window.scrollTo(0, 0);
  };

  render() {
    const {
      feed,
      categoryId,
      categories,
      connectDragSource,
      connectDropTarget,
      showDeletePopup,
      showRenamePopup,
      toggleExportFeed
    } = this.props;
    const feedAttrId = 'sidebar-feed' + feed.id;
    const dragAndDrop = compose(connectDragSource, connectDropTarget);

    return dragAndDrop(
      <li
        id={feedAttrId}
        onClick={this.props.hideParentCategoryDrop}
        className="metismenu-item"
      >
        <a
          href="#"
          className={`metismenu-link feed-icon ${feed.class}`}
          onClick={this.onFeedClick}
        >
          {feed.name}
        </a>

        <i
          tabIndex="0"
          className="metismenu-state-icon font-size-lg opacity-10 pe-7s-more"
          onClick={this.toggleItemDropdown}
        ></i>

        {this.state.isItemDropActive && (
          <SidebarDropdown
            parentAttrId={feedAttrId}
            categories={categories}
            itemId={feed.id}
            itemType={feed.type}
            itemSubType={feed.subType}
            itemName={feed.name}
            itemExported={feed.exported}
            parentId={categoryId}
            showDeletePopup={showDeletePopup}
            showRenamePopup={showRenamePopup}
            toggleExportFeed={toggleExportFeed}
            hideDropDown={this.hideDropDown}
          />
        )}
      </li>
    );
  }
}

const applyDecorators = compose(
  withRouter,
  DragSource(TYPES.FEED, feedSource, dragCollect),
  DropTarget([TYPES.CLIP_ARTICLE], feedTarget, dropCollect),
  onClickOutside
);

export default applyDecorators(Feed);
