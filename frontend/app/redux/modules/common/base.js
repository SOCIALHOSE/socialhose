import reduxModule from '../abstract/reduxModule';
import dashboards, { LOAD_DASHBOARDS } from '../appState/dashboards';
import * as api from '../../../api/usersApi';

export const TOGGLE_UPGRADE_PLAN = 'TOGGLE_UPGRADE_PLAN';

export class Base extends reduxModule {
  getNamespace() {
    return '[Base]';
  }

  changeUserPassword = (password, oldPassword, { token, dispatch }) => {
    dispatch(this.setSettingsPopupError(null));
    return api
      .changePassword(token, { password, oldPassword })
      .then(() => {
        dispatch(this.hideUserSettingsPopup());
      })
      .catch((response) => {
        dispatch(this.setSettingsPopupError(response[0].message));
      });
  };

  defineActions() {
    // const toggleLangsDrop = this.toggle('TOGGLE_LANGS_DROP', 'isLangsDropVisible')
    // const hideLangsDrop = this.reset('HIDE_LANGS_DROP', 'isLangsDropVisible', false)
    // const toggleSidebar = this.toggle('TOGGLE_SIDEBAR', 'isSidebarCollapsed')
    const setDashboardTabs = this.createAction('SET_DASHBOARD_TABS');
    const chooseLanguage = this.createAction('CHOOSE_LANG');
    const showUserSettingsPopup = this.reset(
      'SHOW_USER_SETTINGS_DROP',
      'isSettingsPopupVisible',
      true
    );
    const hideUserSettingsPopup = (this.hideUserSettingsPopup = this.reset(
      'HIDE_USER_SETTINGS_DROP',
      'isSettingsPopupVisible',
      false
    ));
    const changeUserPassword = this.thunkAction(
      'CHANGE_USER_PASSWORD',
      this.changeUserPassword
    );
    const setSettingsPopupError = (this.setSettingsPopupError = this.set(
      'SET_SETTINGS_POPUP_ERROR',
      'settingsPopupError'
    ));
    const toggleResponsiveMenu = this.toggle(
      'TOGGLE_RESPONSIVE_MENU',
      'responsiveMenuVisible'
    );
    const toggleUpgradeModal = this.toggle(
      TOGGLE_UPGRADE_PLAN,
      'isUpgradeVisible'
    );
    const toggleWebTour = this.toggle('TOGGLE_WEBTOUR', 'isTourOpen');

    return {
      // toggleLangsDrop,
      chooseLanguage,
      // hideLangsDrop,
      // toggleSidebar,
      setDashboardTabs,
      showUserSettingsPopup,
      hideUserSettingsPopup,
      changeUserPassword,
      setSettingsPopupError,
      toggleResponsiveMenu,
      toggleUpgradeModal,
      toggleWebTour
    };
  }

  getInitialState() {
    return {
      tabs: {
        /*'dashboard': {
          items: []
        },*/
        search: {
          items: [
            { title: 'search', url: 'search' },
            { title: 'sourceIndex', url: 'source-index' },
            { title: 'sourceLists', url: 'source-lists' }
          ],
          icon: 'pe-7s-search'
        },
        analyze: {
          items: [
            // {title: 'welcome', url: 'welcome'},
            { title: 'savedAnalysis', url: 'saved' },
            { title: 'createAnalysis', url: 'create' }
          ],
          icon: 'pe-7s-graph1'
        },
        share: {
          items: [
            { title: 'notifications', url: 'notifications' },
            { title: 'manageEmails', url: 'manage-emails', masterOnly: true },
            {
              title: 'manageRecipients',
              url: 'manage-recipients',
              masterOnly: true
            },
            { title: 'export', url: 'export' }
          ],
          icon: 'pe-7s-share'
        }
      },
      // isSidebarCollapsed: false,
      isUserSettingsDropVisible: false,
      // isLangsDropVisible: false,
      isSettingsPopupVisible: false,
      settingsPopupError: '',
      isThereSomethingNew: true,
      langs: ['en', 'ar', 'fr'],
      // langs: ['en', 'ar', 'fr', 'es', 'de', , 'he', 'nl', 'pt'],
      activeLang: '',
      rtlLang: false,
      responsiveMenuVisible: false,
      isUpgradeVisible: false
    };
  }

  defineReducers() {
    this.addExternalReducer(
      dashboards.ns(`${LOAD_DASHBOARDS} fulfilled`),
      (state, { payload: dashboards }) => {
        const dashboardTabs = dashboards.map((d) => ({
          url: d.id,
          title: d.name
        }));
        return state.mergeIn(['tabs', 'dashboard', 'items'], dashboardTabs);
      }
    );

    return {
      CHOOSE_LANG: (state, { payload: lang }) => {
        const langsAvailable = state.get('langs');
        const language = langsAvailable.includes(lang) ? lang : 'en';
        const rtlLanguages = ['ar', 'he'];
        const rtlLang = rtlLanguages.includes(language);
        const dir = rtlLang ? 'rtl' : 'ltr';
        document.documentElement.dir = dir; // set page direction
        document.documentElement.lang = language;
        return state.merge({
          activeLang: language,
          rtlLang: rtlLang
        });
      }
    };
  }
}

const instance = new Base();
instance.init();
export default instance;
