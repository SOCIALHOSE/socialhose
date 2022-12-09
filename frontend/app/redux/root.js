import { combineReducers } from 'redux-immutable';

// App state and actions
import register, {
  actions as registerActions
} from './modules/common/register';
import routing from './modules/routing/routing';

import sidebar, { actions as sidebarActions } from './modules/appState/sidebar';
import search, { actions as searchActions } from './modules/appState/search';
import searchByFilters, {
  actions as searchByFiltersActions
} from './modules/appState/searchByFilters';
import sourcesState, {
  actions as sourcesStateActions
} from './modules/appState/sourcesState';
import articles, {
  actions as articleActions
} from './modules/appState/articles';
import shareTabs, {
  actions as shareTabsActions,
  NOTIFICATION_TABLES,
  RECEIVER_TABLES,
  RECIPIENT_FORM_TABLES,
  GROUP_FORM_TABLES,
  NOTIFICATION_SUBSCREENS,
  RECEIVER_SUBSCREENS
} from './modules/appState/share/tabs';
import { actions as shareFormsCommonActions } from './modules/appState/share/shareForms';
import exportFeeds, {
  actions as exportFeedsActions
} from './modules/appState/share/exportFeeds';
import ThemeOptions, { themeActions } from './modules/appState/themeOptions';

//inherited from reduxModule
import auth, { AuthNS, USER_LOGOUT } from './modules/common/auth';
import base from './modules/common/base';
import alerts from './modules/common/alerts';

import dashboards from './modules/appState/dashboards';

import myEmailsTable from './modules/appState/share/tables/myEmailsTable';
import publishedEmailsTable from './modules/appState/share/tables/publishedEmailsTable';
import recipientsTable from './modules/appState/share/tables/recipientsTable';
import groupsTable from './modules/appState/share/tables/groupsTable';
import emailHistoryTable from './modules/appState/share/tables/receiverForm/emailHistoryTable';
import receiverSubscriptionsTable from './modules/appState/share/tables/receiverForm/receiverSubscriptionsTable';
import receiverGroupsTable from './modules/appState/share/tables/receiverForm/receiverGroupsTable';
import receiverRecipientsTable from './modules/appState/share/tables/receiverForm/receiverRecipientsTable';
import emailsTable from './modules/appState/share/tables/emailsTable';
import emailFiltersTable from './modules/appState/share/tables/emailFiltersTable';

import alertForm from './modules/appState/share/forms/alertForm';
import newsletterForm from './modules/appState/share/forms/newsletterForm';
import recipientForm from './modules/appState/share/forms/recipientForm';
import groupForm from './modules/appState/share/forms/groupForm';

import themes from './modules/appState/share/emailThemes/themes';
import analyze, { analyzeActions } from './modules/appState/analyze/analyze';

const shareTables = combineReducers({
  [NOTIFICATION_TABLES.MY_EMAILS]: myEmailsTable.reducers,
  [NOTIFICATION_TABLES.PUBLISHED]: publishedEmailsTable.reducers,
  [RECEIVER_TABLES.RECIPIENTS]: recipientsTable.reducers,
  [RECEIVER_TABLES.GROUPS]: groupsTable.reducers,
  emails: emailsTable.reducers,
  emailFilters: emailFiltersTable.reducers,
  receiverForm: combineReducers({
    [RECIPIENT_FORM_TABLES.GROUPS]: receiverGroupsTable.reducers,
    [RECIPIENT_FORM_TABLES.SUBSCRIPTIONS]: receiverSubscriptionsTable.reducers,
    [RECIPIENT_FORM_TABLES.EMAIL_HISTORY]: emailHistoryTable.reducers,
    [GROUP_FORM_TABLES.RECIPIENTS]: receiverRecipientsTable.reducers
  })
});

const shareForms = combineReducers({
  [NOTIFICATION_SUBSCREENS.ALERT_FORM]: alertForm.reducers,
  [NOTIFICATION_SUBSCREENS.NEWSLETTER_FORM]: newsletterForm.reducers,
  [RECEIVER_SUBSCREENS.RECIPIENT_FORM]: recipientForm.reducers,
  [RECEIVER_SUBSCREENS.GROUP_FORM]: groupForm.reducers
});

const appReducers = combineReducers({
  routing,
  common: combineReducers({
    base: base.reducers,
    auth: auth.reducers,
    alerts: alerts.reducers,
    register
  }),
  appState: combineReducers({
    sidebar,
    search,
    searchByFilters,
    sourcesState,
    analyze,
    articles,
    dashboards: dashboards.reducers,
    themeOptions: ThemeOptions,
    share: combineReducers({
      tabs: shareTabs,
      forms: shareForms,
      tables: shareTables,
      themes: themes.reducers,
      exportFeeds
    })
  })
});

export function rootReducers(state, action) {
  if (action.type === `${AuthNS} ${USER_LOGOUT}`) {
    state = undefined; // to clear state when logout
  }

  return appReducers(state, action);
}

export const shareFormsActions = {
  alert: alertForm.actions,
  newsletter: alertForm.actions,
  recipient: recipientForm.actions,
  group: groupForm.actions
};

export const shareTablesActions = {
  [NOTIFICATION_TABLES.MY_EMAILS]: myEmailsTable.actions,
  [NOTIFICATION_TABLES.PUBLISHED]: publishedEmailsTable.actions,
  [RECEIVER_TABLES.RECIPIENTS]: recipientsTable.actions,
  [RECEIVER_TABLES.GROUPS]: groupsTable.actions,
  emails: emailsTable.actions,
  emailFilters: emailFiltersTable.actions,
  receiverForm: {
    [RECIPIENT_FORM_TABLES.GROUPS]: receiverGroupsTable.actions,
    [RECIPIENT_FORM_TABLES.SUBSCRIPTIONS]: receiverSubscriptionsTable.actions,
    [RECIPIENT_FORM_TABLES.EMAIL_HISTORY]: emailHistoryTable.actions,
    [GROUP_FORM_TABLES.RECIPIENTS]: receiverRecipientsTable.actions
  }
};

export const rootActions = Object.assign(
  {},
  auth.actions,
  alerts.actions,
  base.actions,
  registerActions,
  sidebarActions,
  searchActions,
  analyzeActions,
  searchByFiltersActions,
  sourcesStateActions,
  shareTabsActions,
  shareFormsCommonActions,
  themes.actions,
  themeActions,
  articleActions,
  exportFeedsActions,
  dashboards.actions,
  { shareTables: shareTablesActions },
  { shareForms: shareFormsActions }
);
