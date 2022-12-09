import React from 'react'
import PropTypes from 'prop-types'
import $ from 'jquery'
import { translate } from 'react-i18next'

export class SidebarDropdown extends React.Component {
  static propTypes = {
    itemName: PropTypes.string.isRequired,
    itemSubType: PropTypes.string.isRequired,
    itemType: PropTypes.string.isRequired,
    itemId: PropTypes.number.isRequired,
    itemExported: PropTypes.bool,
    parentId: PropTypes.number.isRequired,
    parentAttrId: PropTypes.string.isRequired,
    showDeletePopup: PropTypes.func.isRequired,
    showRenamePopup: PropTypes.func.isRequired,
    showAddCategoryPopup: PropTypes.func,
    showAddClippingsPopup: PropTypes.func,
    toggleExportFeed: PropTypes.func,
    toggleExportCategory: PropTypes.func,
    t: PropTypes.func.isRequired,
    hideDropDown: PropTypes.func.isRequired
  };

  constructor (props) {
    super(props)
    this.state = {
      dropdownTopPos: 'auto',
      dropdownBottomPos: 'auto',
      dropdownOpacity: 0
    }
  }

  componentDidMount = () => {
    const topPos = $('#' + this.props.parentAttrId).offset().top - $(document).scrollTop()
    const dropdownHeight = $('#sidebar-category-dropdown').height()

    if ($(window).height() - topPos >= dropdownHeight) {
      this.setState({
        dropdownTopPos: topPos,
        dropdownOpacity: 1
      })
    } else {
      this.setState({
        dropdownBottomPos: 5,
        dropdownOpacity: 1
      })
    }
  };

  onExportToggle = () => {
    const {itemId, toggleExportFeed, itemExported, hideDropDown} = this.props
    toggleExportFeed(itemId, !itemExported)
    hideDropDown()
  };

  onExportCategoryToggle = () => {
    const {itemId, toggleExportCategory, itemExported, hideDropDown} = this.props
    toggleExportCategory(itemId, !itemExported)
    hideDropDown()
  };

  onDelete = () => {
    this.props.showDeletePopup(this.props.itemId, this.props.itemType, this.props.itemName, this.props.parentId)
  };

  onRename = () => {
    this.props.showRenamePopup(this.props.itemId, this.props.itemType, this.props.itemName, this.props.parentId)
  };

  onAddCategory = () => {
    // set this item id as parent of new category
    this.props.showAddCategoryPopup(this.props.itemId)
  };

  onAddClippingsFeedPopup = () => {
    this.props.showAddClippingsPopup(this.props.itemId)
  };

  render () {
    const { itemSubType, t, itemExported } = this.props
    let dropdown

    switch (itemSubType) {
      case 'my_content':
        dropdown = <ul id="sidebar-category-dropdown" className="sidebar-category__dropdown" style={{top: this.state.dropdownTopPos, bottom: this.state.dropdownBottomPos, opacity: this.state.dropdownOpacity}}>
          <li><a href="#" onClick={this.onAddClippingsFeedPopup}>{t('sidebarDropdown.AddClippingsFeed')}</a></li>
          <li><a href="#" onClick={this.onAddCategory}>{t('sidebarDropdown.AddFolder')}</a></li>
          {/*<li><a href="#">{t('sidebarDropdown.DownloadSearchCriteria')}</a></li>
          <li><a href="#">{t('sidebarDropdown.EditSearchTemplate')}</a></li>*/}
          <li><a href="#" onClick={this.onExportCategoryToggle}>{t(itemExported ? 'sidebarDropdown.UnexportFeeds' : 'sidebarDropdown.ExportFeeds')}</a></li>
          {/*<li><a href="#">{t('sidebarDropdown.ViewUserComments')}</a></li>*/}
        </ul>
        break

      case 'shared_content':
        dropdown = <ul id="sidebar-category-dropdown" className="sidebar-category__dropdown" style={{top: this.state.dropdownTopPos, bottom: this.state.dropdownBottomPos, opacity: this.state.dropdownOpacity}}>
          <li><a href="#" onClick={this.onAddClippingsFeedPopup}>{t('sidebarDropdown.AddClippingsFeed')}</a></li>
          <li><a href="#" onClick={this.onAddCategory}>{t('sidebarDropdown.AddFolder')}</a></li>
          {/*<li><a href="#">{t('sidebarDropdown.DownloadSearchCriteria')}</a></li>*/}
          <li><a href="#" onClick={this.onExportCategoryToggle}>{t(itemExported ? 'sidebarDropdown.UnexportFeeds' : 'sidebarDropdown.ExportFeeds')}</a></li>
          {/*<li><a href="#">{t('sidebarDropdown.ViewUserComments')}</a></li>*/}
        </ul>
        break

      case 'custom':
        dropdown = <ul id="sidebar-category-dropdown" className="sidebar-category__dropdown" style={{top: this.state.dropdownTopPos, bottom: this.state.dropdownBottomPos, opacity: this.state.dropdownOpacity}}>
          <li><a href="#" onClick={this.onAddClippingsFeedPopup}>{t('sidebarDropdown.AddClippingsFeed')}</a></li>
          <li><a href="#" onClick={this.onAddCategory}>{t('sidebarDropdown.AddFolder')}</a></li>
          {/*<li><a href="#">{t('sidebarDropdown.DownloadSearchCriteria')}</a></li>*/}
          <li><a href="#" onClick={this.onExportCategoryToggle}>{t(itemExported ? 'sidebarDropdown.UnexportFeeds' : 'sidebarDropdown.ExportFeeds')}</a></li>
          <li><a href="#" onClick={this.onRename}>{t('sidebarDropdown.RenameFolder')}</a></li>
          {/*<li><a href="#">{t('sidebarDropdown.ViewUserComments')}</a></li>*/}
          <li><a href="#" onClick={this.onDelete}>{t('sidebarDropdown.DeleteFolder')}</a></li>
        </ul>
        break

      case 'query_feed':
        dropdown = <ul id="sidebar-category-dropdown" className="sidebar-category__dropdown" style={{top: this.state.dropdownTopPos, bottom: this.state.dropdownBottomPos, opacity: this.state.dropdownOpacity}}>
          {/*<li><a href="#">{t('sidebarDropdown.AddArticle')}</a></li>
          <li><a href="#">{t('sidebarDropdown.AddToDashboard')}</a></li>
          <li><a href="#">{t('sidebarDropdown.AnalyzeFeed')}</a></li>
          <li><a href="#">{t('sidebarDropdown.DownloadArticleData')}</a></li>
          <li><a href="#">{t('sidebarDropdown.DownloadFeedStatistics')}</a></li>
          <li><a href="#">{t('sidebarDropdown.DownloadSearchCriteria')}</a></li>*/}
          <li><a href="#" onClick={this.onExportToggle}>{t(itemExported ? 'sidebarDropdown.UnexportFeed' : 'sidebarDropdown.ExportFeed')}</a></li>
          <li><a href="#" onClick={this.onRename}>{t('sidebarDropdown.RenameFeed')}</a></li>
          <li><a href="#" onClick={this.onDelete}>{t('sidebarDropdown.DeleteFeed')}</a></li>
        </ul>
        break

      case 'clip_feed':
        dropdown = <ul id="sidebar-category-dropdown" className="sidebar-category__dropdown" style={{top: this.state.dropdownTopPos, bottom: this.state.dropdownBottomPos, opacity: this.state.dropdownOpacity}}>
          {/*<li><a href="#">{t('sidebarDropdown.AddArticle')}</a></li>
          <li><a href="#">{t('sidebarDropdown.AddToDashboard')}</a></li>
          <li><a href="#">{t('sidebarDropdown.AnalyzeFeed')}</a></li>
          <li><a href="#">{t('sidebarDropdown.DownloadArticleData')}</a></li>
          <li><a href="#">{t('sidebarDropdown.DownloadFeedStatistics')}</a></li>
          <li><a href="#">{t('sidebarDropdown.DownloadSearchCriteria')}</a></li>*/}
          <li><a href="#" onClick={this.onExportToggle}>{t(itemExported ? 'sidebarDropdown.UnexportFeed' : 'sidebarDropdown.ExportFeed')}</a></li>
          <li><a href="#" onClick={this.onRename}>{t('sidebarDropdown.RenameFeed')}</a></li>
          <li><a href="#" onClick={this.onDelete}>{t('sidebarDropdown.DeleteFeed')}</a></li>
        </ul>
        break

    }

    return (
      dropdown
    )
  }
}

export default translate(['common'], { wait: true })(SidebarDropdown)
