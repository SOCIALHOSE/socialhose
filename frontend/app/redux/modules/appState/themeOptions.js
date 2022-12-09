import sideBar6 from '../../../styles/utils/images/sidebar/city1.jpg'

export const SET_ENABLE_BACKGROUND_IMAGE =
  'THEME_OPTIONS/SET_ENABLE_BACKGROUND_IMAGE'

export const SET_ENABLE_MOBILE_MENU = 'THEME_OPTIONS/SET_ENABLE_MOBILE_MENU'
export const SET_ENABLE_MOBILE_MENU_SMALL =
  'THEME_OPTIONS/SET_ENABLE_MOBILE_MENU_SMALL'

export const SET_ENABLE_FIXED_HEADER = 'THEME_OPTIONS/SET_ENABLE_FIXED_HEADER'
export const SET_ENABLE_HEADER_SHADOW = 'THEME_OPTIONS/SET_ENABLE_HEADER_SHADOW'
export const SET_ENABLE_SIDEBAR_SHADOW =
  'THEME_OPTIONS/SET_ENABLE_SIDEBAR_SHADOW'
export const SET_ENABLE_FIXED_SIDEBAR = 'THEME_OPTIONS/SET_ENABLE_FIXED_SIDEBAR'
export const SET_ENABLE_CLOSED_SIDEBAR =
  'THEME_OPTIONS/SET_ENABLE_CLOSED_SIDEBAR'
export const SET_ENABLE_FIXED_FOOTER = 'THEME_OPTIONS/SET_ENABLE_FIXED_FOOTER'

export const SET_ENABLE_PAGETITLE_ICON =
  'THEME_OPTIONS/SET_ENABLE_PAGETITLE_ICON'
export const SET_ENABLE_PAGETITLE_SUBHEADING =
  'THEME_OPTIONS/SET_ENABLE_PAGETITLE_SUBHEADING'
export const SET_ENABLE_PAGE_TABS_ALT = 'THEME_OPTIONS/SET_ENABLE_PAGE_TABS_ALT'

export const SET_BACKGROUND_IMAGE = 'THEME_OPTIONS/SET_BACKGROUND_IMAGE'
export const SET_BACKGROUND_COLOR = 'THEME_OPTIONS/SET_BACKGROUND_COLOR'
export const SET_COLOR_SCHEME = 'THEME_OPTIONS/SET_COLOR_SCHEME'
export const SET_BACKGROUND_IMAGE_OPACITY =
  'THEME_OPTIONS/SET_BACKGROUND_IMAGE_OPACITY'

export const SET_HEADER_BACKGROUND_COLOR =
  'THEME_OPTIONS/SET_HEADER_BACKGROUND_COLOR'

const setEnableBackgroundImage = (enableBackgroundImage) => ({
  type: SET_ENABLE_BACKGROUND_IMAGE,
  enableBackgroundImage
})

const setEnableFixedHeader = (enableFixedHeader) => ({
  type: SET_ENABLE_FIXED_HEADER,
  enableFixedHeader
})

const setEnableHeaderShadow = (enableHeaderShadow) => ({
  type: SET_ENABLE_HEADER_SHADOW,
  enableHeaderShadow
})

const setEnableSidebarShadow = (enableSidebarShadow) => ({
  type: SET_ENABLE_SIDEBAR_SHADOW,
  enableSidebarShadow
})

const setEnablePageTitleIcon = (enablePageTitleIcon) => ({
  type: SET_ENABLE_PAGETITLE_ICON,
  enablePageTitleIcon
})

const setEnablePageTitleSubheading = (enablePageTitleSubheading) => ({
  type: SET_ENABLE_PAGETITLE_SUBHEADING,
  enablePageTitleSubheading
})

const setEnablePageTabsAlt = (enablePageTabsAlt) => ({
  type: SET_ENABLE_PAGE_TABS_ALT,
  enablePageTabsAlt
})

const setEnableFixedSidebar = (enableFixedSidebar) => ({
  type: SET_ENABLE_FIXED_SIDEBAR,
  enableFixedSidebar
})

const setEnableClosedSidebar = (enableClosedSidebar) => ({
  type: SET_ENABLE_CLOSED_SIDEBAR,
  enableClosedSidebar
})

const setEnableMobileMenu = (enableMobileMenu) => ({
  type: SET_ENABLE_MOBILE_MENU,
  enableMobileMenu
})

const setEnableMobileMenuSmall = (enableMobileMenuSmall) => ({
  type: SET_ENABLE_MOBILE_MENU_SMALL,
  enableMobileMenuSmall
})

const setEnableFixedFooter = (enableFixedFooter) => ({
  type: SET_ENABLE_FIXED_FOOTER,
  enableFixedFooter
})

const setBackgroundColor = (backgroundColor) => ({
  type: SET_BACKGROUND_COLOR,
  backgroundColor
})

const setHeaderBackgroundColor = (headerBackgroundColor) => ({
  type: SET_HEADER_BACKGROUND_COLOR,
  headerBackgroundColor
})

const setColorScheme = (colorScheme) => ({
  type: SET_COLOR_SCHEME,
  colorScheme
})

const setBackgroundImageOpacity = (backgroundImageOpacity) => ({
  type: SET_BACKGROUND_IMAGE_OPACITY,
  backgroundImageOpacity
})

const setBackgroundImage = (backgroundImage) => ({
  type: SET_BACKGROUND_IMAGE,
  backgroundImage
})

export const themeActions = {
  setEnableBackgroundImage,
  setEnableFixedHeader,
  setEnableHeaderShadow,
  setEnableSidebarShadow,
  setEnablePageTitleIcon,
  setEnablePageTitleSubheading,
  setEnablePageTabsAlt,
  setEnableFixedSidebar,
  setEnableClosedSidebar,
  setEnableMobileMenu,
  setEnableMobileMenuSmall,
  setEnableFixedFooter,
  setBackgroundColor,
  setHeaderBackgroundColor,
  setColorScheme,
  setBackgroundImageOpacity,
  setBackgroundImage
}

export default function ThemeOptions (
  state = {
    backgroundColor: '',
    headerBackgroundColor: '',
    enableMobileMenuSmall: '',
    enableBackgroundImage: false,
    enableClosedSidebar: false,
    enableFixedHeader: true,
    enableHeaderShadow: true,
    enableSidebarShadow: true,
    enableFixedFooter: true,
    enableFixedSidebar: true,
    colorScheme: 'white',
    backgroundImage: sideBar6,
    backgroundImageOpacity: 'opacity-06',
    enablePageTitleIcon: true,
    enablePageTitleSubheading: true,
    enablePageTabsAlt: true
  },
  action
) {
  switch (action.type) {
    case SET_ENABLE_BACKGROUND_IMAGE:
      return {
        ...state,
        enableBackgroundImage: action.enableBackgroundImage
      }

    case SET_ENABLE_FIXED_HEADER:
      return {
        ...state,
        enableFixedHeader: action.enableFixedHeader
      }

    case SET_ENABLE_HEADER_SHADOW:
      return {
        ...state,
        enableHeaderShadow: action.enableHeaderShadow
      }

    case SET_ENABLE_SIDEBAR_SHADOW:
      return {
        ...state,
        enableSidebarShadow: action.enableSidebarShadow
      }

    case SET_ENABLE_PAGETITLE_ICON:
      return {
        ...state,
        enablePageTitleIcon: action.enablePageTitleIcon
      }

    case SET_ENABLE_PAGETITLE_SUBHEADING:
      return {
        ...state,
        enablePageTitleSubheading: action.enablePageTitleSubheading
      }

    case SET_ENABLE_PAGE_TABS_ALT:
      return {
        ...state,
        enablePageTabsAlt: action.enablePageTabsAlt
      }

    case SET_ENABLE_FIXED_SIDEBAR:
      return {
        ...state,
        enableFixedSidebar: action.enableFixedSidebar
      }

    case SET_ENABLE_MOBILE_MENU:
      return {
        ...state,
        enableMobileMenu: action.enableMobileMenu
      }

    case SET_ENABLE_MOBILE_MENU_SMALL:
      return {
        ...state,
        enableMobileMenuSmall: action.enableMobileMenuSmall
      }

    case SET_ENABLE_CLOSED_SIDEBAR:
      return {
        ...state,
        enableClosedSidebar: action.enableClosedSidebar
      }

    case SET_ENABLE_FIXED_FOOTER:
      return {
        ...state,
        enableFixedFooter: action.enableFixedFooter
      }

    case SET_BACKGROUND_COLOR:
      return {
        ...state,
        backgroundColor: action.backgroundColor
      }

    case SET_HEADER_BACKGROUND_COLOR:
      return {
        ...state,
        headerBackgroundColor: action.headerBackgroundColor
      }

    case SET_COLOR_SCHEME:
      return {
        ...state,
        colorScheme: action.colorScheme
      }

    case SET_BACKGROUND_IMAGE:
      return {
        ...state,
        backgroundImage: action.backgroundImage
      }

    case SET_BACKGROUND_IMAGE_OPACITY:
      return {
        ...state,
        backgroundImageOpacity: action.backgroundImageOpacity
      }
    default:
  }
  return state
}
