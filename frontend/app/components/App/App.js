import React from 'react';
import PropTypes from 'prop-types';
import { compose } from 'redux';
import { withRouter } from 'react-router-dom';
import { HTML5Backend } from 'react-dnd-html5-backend';
import { DndProvider } from 'react-dnd';
import { isMobile } from 'react-device-detect';
import { TouchBackend } from 'react-dnd-touch-backend';
import cx from 'classnames';
import echarts from 'echarts';
import ResizeDetector from 'react-resize-detector';

import AppHeader from './AppHeader/AppHeader';
import WebTour from './AppHeader/WebTour';
import Sidebar from './Sidebar/Sidebar';
import reduxConnect from '../../redux/utils/connect';
// import { NOTIFICATION_SUBSCREENS } from '../../redux/modules/appState/share/tabs';
import LoadersAdvanced from '../common/Loader/Loader';
import WesteronTheme from '../common/charts/WesterosTheme.json';
import 'react-dates/initialize';
import 'react-dates/lib/css/_datepicker.css';
import Footer from '../common/Footer';
import { Button, UncontrolledTooltip } from 'reactstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faQuestion } from '@fortawesome/free-solid-svg-icons';
import { find, map } from 'lodash';
import tourPages from './AppHeader/WebTourSteps';
import { allMediaTypes } from '../../redux/modules/appState/searchByFilters';
import { translate } from 'react-i18next';
import i18n from '../../i18n';
import * as timeago from 'timeago.js';

import ar from 'timeago.js/lib/lang/ar';
import fr from 'timeago.js/lib/lang/fr';

// register it languages for time-ago.
timeago.register('ar', ar);
timeago.register('fr', fr);

const DnDBackend = isMobile ? TouchBackend : HTML5Backend;

class App extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired,
    children: PropTypes.element,
    history: PropTypes.object.isRequired,
    location: PropTypes.object.isRequired,
    store: PropTypes.object.isRequired
  };

  state = {
    showSidebar: true,
    sidebarAnimationDisabled: true,
    closedSmallerSidebar: false,
    showTourIcon: false
  };

  componentDidMount() {
    echarts.registerTheme('westeros', WesteronTheme);
    this.checkIfTourGuide();

    const {
      common: { auth }
    } = this.props.store;

    const activeLang = i18n.language.slice(0, 2);
    this.props.actions.chooseLanguage(activeLang);

    if (
      auth &&
      auth.user &&
      auth.user.restrictions &&
      auth.user.restrictions.plans
    ) {
      const planDetails = auth.user.restrictions.plans;
      let allowedMediaTypes = allMediaTypes.filter((v) => planDetails[v]);
      /*if (auth.user.restrictions.plans.price === 0) {
        // TODO: remove following restrictions when duplication fixes
        const restrictedTemporary = ['news', 'blogs'];
        allowedMediaTypes = allowedMediaTypes.filter(
          (v) => !restrictedTemporary.includes(v)
        );
      } */
      this.props.actions.toggleMediaType(allowedMediaTypes, true);
    } else {
      this.props.actions.toggleMediaType([], true);
    }
  }

  componentDidUpdate(prevProps) {
    if (prevProps.location.pathname !== this.props.location.pathname) {
      this.checkIfTourGuide();
    }
  }

  checkIfTourGuide = () => {
    const tourCurrentPaths = map(tourPages, 'showOn');
    const hasTour = tourCurrentPaths.some((path) =>
      this.props.location.pathname.startsWith(path)
    );

    if (hasTour) {
      !this.state.showTourIcon && this.setState({ showTourIcon: true });
      return;
    }

    this.state.showTourIcon && this.setState({ showTourIcon: false });
  };

  showWebTour = () => {
    const tourSendPaths = find(tourPages, (o) =>
      this.props.location.pathname.startsWith(o.showOn)
    );

    if (tourSendPaths) {
      // Open in a new tab to reset every redux state
      const win = window.open(`${tourSendPaths.to}?webtour=true`, '_blank');
      win.focus();
    }
  };

  render() {
    const { store, actions, children, t } = this.props;
    const { common: commonState, appState } = store;
    const { sidebar, themeOptions } = appState;
    const { base, auth } = commonState;

    const {
      colorScheme,
      enableFixedHeader,
      enableFixedSidebar,
      enableFixedFooter,
      enableClosedSidebar,
      closedSmallerSidebar,
      enableMobileMenu,
      enablePageTabsAlt
    } = themeOptions;

    if (!auth.token) {
      <LoadersAdvanced />;
    }

    return (
      <ResizeDetector
        handleWidth
        // eslint-disable-next-line react/jsx-no-bind
        render={({ width }) => {
          return (
            <DndProvider backend={DnDBackend}>
              <div
                className={cx(
                  'app-container app-theme-' + colorScheme,
                  { 'fixed-header': enableFixedHeader },
                  { 'fixed-sidebar': enableFixedSidebar || width < 1250 },
                  { 'fixed-footer': enableFixedFooter },
                  { 'closed-sidebar': enableClosedSidebar || width < 1250 },
                  {
                    'closed-sidebar-mobile':
                      closedSmallerSidebar || width < 1250
                  },
                  { 'sidebar-mobile-open': enableMobileMenu },
                  { 'body-tabs-shadow-btn': enablePageTabsAlt }
                )}
              >
                {this.state.showTourIcon && (
                  <div>
                    <Button
                      id="GuidedTour"
                      className="floating-icon"
                      color="warning"
                      onClick={this.showWebTour}
                    >
                      <FontAwesomeIcon
                        icon={faQuestion}
                        color="#573a04"
                        fixedWidth={false}
                        size="2x"
                      />
                    </Button>
                    <UncontrolledTooltip placement="left" target={'GuidedTour'}>
                      {t('userSettings.guidedTourTooltip')}
                    </UncontrolledTooltip>
                  </div>
                )}
                <AppHeader
                  appCommonState={base}
                  userFirstName={auth.user.firstName}
                  userLastName={auth.user.lastName}
                  restrictions={auth.user.restrictions}
                  userRole={auth.user.role}
                  actions={actions}
                  themeOptions={themeOptions}
                />

                <div className="app-main">
                  <Sidebar
                    t={t}
                    sidebarState={sidebar}
                    themeOptions={themeOptions}
                    actions={actions}
                  />
                  <div className="app-main__outer">
                    <div className="app-main__inner">
                      {children}
                      <Footer />
                    </div>
                  </div>
                </div>
                <WebTour />
              </div>
            </DndProvider>
          );
        }}
      />
    );
  }
}

const applyDecorators = compose(
  translate(['common'], { wait: true }),
  withRouter,
  reduxConnect()
);

export default applyDecorators(App);
