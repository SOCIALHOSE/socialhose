import React from 'react';
import cx from 'classnames';
import PropTypes from 'prop-types';
import SidebarDropdown from './SidebarDropdown';
import { translate } from 'react-i18next';

class CategoryHead extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    showDeletePopup: PropTypes.func.isRequired,
    showRenamePopup: PropTypes.func.isRequired,
    showAddCategoryPopup: PropTypes.func.isRequired,
    showAddClippingsFeedPopup: PropTypes.func.isRequired,
    toggleCollapse: PropTypes.func.isRequired,
    toggleCategoryDropdown: PropTypes.func.isRequired,
    toggleExportCategory: PropTypes.func.isRequired,
    isCategoryDropActive: PropTypes.bool.isRequired,
    isCategoryActive: PropTypes.bool.isRequired,
    hideDropDown: PropTypes.func.isRequired,
    parentId: PropTypes.number.isRequired,
    category: PropTypes.object.isRequired,
    categories: PropTypes.array.isRequired
  };

  getSidebarName(name) {
    const catName = this.props.t(`sidebar.${name}`);
    if (catName === `sidebar.${name}`) {
      return name;
    }
    return catName;
  }

  render() {
    const {
      isCategoryActive,
      isCategoryDropActive,
      category,
      categories,
      showDeletePopup,
      showRenamePopup,
      showAddCategoryPopup,
      showAddClippingsFeedPopup,
      toggleExportCategory,
      hideDropDown
    } = this.props;

    const isCategoryDeletedType = category.subType === 'deleted_content';
    const categoryAttrId = 'sidebar-category' + category.id;

    return (
      <div
        className="metismenu-link"
        id={categoryAttrId}
        onClick={this.props.toggleCollapse}
      >
        {/* <i className="sidebar-category__closed-icon" onClick={this.props.toggleCollapse}> </i>
        <i className="sidebar-category__open-icon" onClick={this.props.toggleCollapse}> </i> */}

        {isCategoryDeletedType ? (
          <i className="metismenu-icon pe-7s-trash"></i>
        ) : (
          <i className="metismenu-icon pe-7s-folder"></i>
        )}

        {this.getSidebarName(category.name)}

        {!isCategoryDeletedType && (
          <i
            tabIndex="0"
            className="metismenu-state-icon font-size-lg opacity-10 pe-7s-more mr-4"
            onClick={this.props.toggleCategoryDropdown}
          />
        )}
        <i
          className={cx(
            'metismenu-state-icon pe-7s-angle-down pointer-events-none opacity-10',
            {
              'rotate-minus-90': isCategoryActive
            }
          )}
        />

        {isCategoryDropActive && (
          <SidebarDropdown
            parentAttrId={categoryAttrId}
            categories={categories}
            itemId={category.id}
            itemSubType={category.subType}
            itemType={category.type}
            itemName={category.name}
            parentId={this.props.parentId}
            showDeletePopup={showDeletePopup}
            showRenamePopup={showRenamePopup}
            showAddCategoryPopup={showAddCategoryPopup}
            showAddClippingsPopup={showAddClippingsFeedPopup}
            hideDropDown={hideDropDown}
            toggleExportCategory={toggleExportCategory}
          />
        )}
      </div>
    );
  }
}

export default translate(['common'], { wait: true })(CategoryHead);
