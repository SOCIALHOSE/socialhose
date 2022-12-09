import React, { useEffect } from 'react';
import { Redirect, Route, Switch } from 'react-router-dom';
import PropTypes from 'prop-types';
import reduxConnect from '../redux/utils/connect';
import LoadersAdvanced from '../components/common/Loader/Loader';

import i18n from '../i18n';
import { Slide, ToastContainer } from 'react-toastify';
import UnauthenticatedRoute from './UnauthenticatedRoute';
import AuthenticatedRoute from './AuthenticatedRoute';
import usePageTracking from '../components/common/hooks/usePageTracking';
import { getIP, setIP } from '../common/helper';

function AppRoutes(props) {
  usePageTracking();
  const {
    common: { auth, base }
  } = props;
  const authIsPending = auth.isAuthPending;

  useEffect(() => {
    const { actions } = props;
    actions.handleErrors();
    actions.refreshLogin();

    if (!getIP()) {
      setIP(); // set IP to use when submit HubSpot form
    }

    //Set active language after load
    const activeLang = i18n.language.slice(0, 2);
    actions.chooseLanguage(activeLang);
  }, []);

  return (
    <div className="root-layout">
      {authIsPending && <LoadersAdvanced />}

      {!authIsPending && (
        <Switch>
          <Route path="/app/:activeTab" component={AuthenticatedRoute} />
          <Route path="/" component={UnauthenticatedRoute} />
          <Redirect to="/auth/login" />
        </Switch>
      )}

      <ToastContainer
        pauseOnHover
        closeOnClick={false}
        position="top-right"
        transition={Slide}
        draggable={false}
        closeButton={CloseButton}
        rtl={base.rtlLang}
      />
    </div>
  );
}

const CloseButton = ({ closeToast }) => (
  <button
    className="Toastify__close-button"
    type="button"
    aria-label="close"
    onClick={closeToast}
  >
    ✖︎
  </button>
);

CloseButton.propTypes = {
  closeToast: PropTypes.func.isRequired
};

AppRoutes.propTypes = {
  common: PropTypes.object.isRequired,
  actions: PropTypes.object.isRequired,
  children: PropTypes.object
};

export default reduxConnect('common', ['common'])(AppRoutes);
