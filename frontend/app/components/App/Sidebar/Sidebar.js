import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import cx from 'classnames'
import Categories from './Categories'
import Filter from './Filter'
import DeletePopup from './DeletePopup'
import RenamePopup from './RenamePopup'
import AddCategoryPopup from './AddCategoryPopup'
import AddClippingsFeedPopup from './AddClippingsFeedPopup'
import LoadersAdvanced from '../../common/Loader/Loader'
import CSSTransitionGroup from 'react-transition-group/CSSTransitionGroup'
import PerfectScrollbar from 'react-perfect-scrollbar'
import HeaderLogo from '../AppHeader/HeaderLogo'

export class Sidebar extends React.Component {
  static propTypes = {
    actions: PropTypes.object.isRequired,
    themeOptions: PropTypes.object.isRequired,
    backgroundColor: PropTypes.string,
    backgroundImage: PropTypes.any,
    backgroundImageOpacity: PropTypes.any,
    enableBackgroundImage: PropTypes.any,
    enableMobileMenu: PropTypes.any,
    enableSidebarShadow: PropTypes.any,
    setEnableMobileMenu: PropTypes.func,
    t: PropTypes.func,
    sidebarState: PropTypes.object.isRequired
  }

  constructor (props) {
    super(props)
    this.state = {
      sidebarAnimationDisabled: true,
      activeSearch: false
    }
  }

  toggleMobileSidebar = () => {
    let { enableMobileMenu, setEnableMobileMenu } = this.props
    setEnableMobileMenu(!enableMobileMenu)
  }

  componentDidMount = () => {
    this.props.actions.getSidebarCategories()
  }

  activeSearchFunc = () => {
    this.setState({ activeSearch: !this.state.activeSearch })
  }

  render () {
    let {
      backgroundColor,
      enableBackgroundImage,
      enableSidebarShadow,
      backgroundImage,
      backgroundImageOpacity
    } = this.props.themeOptions

    const { sidebarState, actions } = this.props

    return (
      <Fragment>
        <div
          className="sidebar-mobile-overlay"
          onClick={this.toggleMobileSidebar}
        />
        <CSSTransitionGroup
          component="div"
          className={cx('app-sidebar', backgroundColor, {
            'sidebar-shadow': enableSidebarShadow
          })}
          transitionName="SidebarAnimation"
          transitionAppear
          transitionAppearTimeout={1500}
          transitionEnter={false}
          transitionLeave={false}
        >
          <HeaderLogo />
          {!sidebarState.areCategoriesLoaded && <LoadersAdvanced />}
          <PerfectScrollbar>
            <div className="app-sidebar__inner mt-3">
              <div className="vertical-nav-menu" data-tour="left-panel">
                <div className="metismenu-container">
                  <Filter
                    t={this.props.t}
                    categories={sidebarState.categories}
                    areFeedsFiltered={sidebarState.areFeedsFiltered}
                    setFilteredCategories={actions.setFilteredCategories}
                    clearFilteredCategories={actions.clearFilteredCategories}
                  />

                  <Categories
                    actions={actions}
                    areCategoriesLoaded={sidebarState.areCategoriesLoaded}
                    areFeedsFiltered={sidebarState.areFeedsFiltered}
                    categories={sidebarState.categories}
                    filteredCategories={sidebarState.filteredCategories}
                  />
                </div>

                {sidebarState.popupVisible.delete && (
                  <DeletePopup
                    hideDeletePopup={actions.hideDeletePopup}
                    deleteFeed={actions.deleteFeed}
                    deleteCategory={actions.deleteCategory}
                    itemToDelete={sidebarState.popupItems.delete}
                  />
                )}

                {sidebarState.popupVisible.rename && (
                  <RenamePopup
                    addAlert={actions.addAlert}
                    hideRenamePopup={actions.hideRenamePopup}
                    renameFeed={actions.renameFeed}
                    renameCategory={actions.renameCategory}
                    itemToRename={sidebarState.popupItems.rename}
                  />
                )}

                {sidebarState.popupVisible.addCategory && (
                  <AddCategoryPopup
                    hideAddCategoryPopup={actions.hideAddCategoryPopup}
                    addCategory={actions.addCategory}
                    parentId={sidebarState.popupItems.addCategory.parentId}
                  />
                )}

                {sidebarState.popupVisible.addClippingsFeed && (
                  <AddClippingsFeedPopup
                    parentId={sidebarState.popupItems.addClippingsFeed.parentId}
                    hidePopup={actions.hideAddClippingsFeedPopup}
                    addClippingsFeed={actions.addClippingsFeed}
                    addAlert={actions.addAlert}
                    categories={sidebarState.categories}
                  />
                )}
              </div>
            </div>
          </PerfectScrollbar>
          <div
            className={cx('app-sidebar-bg', backgroundImageOpacity)}
            style={{
              backgroundImage: enableBackgroundImage
                ? 'url(' + backgroundImage + ')'
                : null
            }}
          ></div>
        </CSSTransitionGroup>
      </Fragment>
    )
  }
}

export default React.memo(Sidebar)
