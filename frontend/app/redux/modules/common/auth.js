import * as api from '../../../api/loginApi';
import $ from 'jquery';
import reduxModule from '../abstract/reduxModule';
import { tokenInject } from '../../utils/common';
import { addAlert } from './alerts';
import Cookies from 'cookies-js';
import axios from 'axios';
// import { TOGGLE_UPGRADE_PLAN } from './base';

const ACTIONS = {
  PENDING: 'Login pending',
  SAVE_USER_DATA: 'Save user data',
  SET_FORM_ERROR: 'Set form error',
  SET_RESTRICTIONS: 'Set user restrictions'
};

export const AuthNS = '[Auth]';
export const USER_LOGOUT = 'Logout user';
const REFRESH_TOKEN = 'refreshToken';

class Auth extends reduxModule {
  getNamespace() {
    return AuthNS;
  }

  getInitialState() {
    return {
      form: {
        error: ''
      },
      isAuthPending: true,
      token: '',
      refreshToken: '',
      user: {},
      userSubscription: '15d',
      userSubscriptionDate: '2017-03-01'
    };
  }

  saveRefreshToken(refreshToken, rememberMe) {
    if (rememberMe) {
      localStorage.setItem(REFRESH_TOKEN, refreshToken);
    } else {
      Cookies.set(REFRESH_TOKEN, refreshToken);
    }
  }

  clearRefreshToken() {
    localStorage.removeItem(REFRESH_TOKEN);
    delete axios.defaults.headers.common['Authorization'];
    Cookies.expire(REFRESH_TOKEN);
  }

  getRefreshToken() {
    return Cookies.get(REFRESH_TOKEN) || localStorage.getItem(REFRESH_TOKEN);
  }

  loginRequest(dispatch, promise, rememberMe) {
    dispatch(this.loginPending(true));
    return promise
      .then((data) => {
        const { token, refreshToken, user } = data;
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`; // to call api with axios
        this.saveRefreshToken(data.refreshToken, rememberMe);
        dispatch(this.saveUserData({ token, refreshToken, user }));
        dispatch(this.loginPending(false));
        dispatch(this.authSetError(''));
        // history.replace(location); //rerun auth guards for routes
      })
      .catch((error) => {
        dispatch(this.authSetError(error.msg));
        dispatch(this.loginPending(false));
        delete axios.defaults.headers.common['Authorization'];
        // history.replace(location); //rerun auth guards for routes
      });
  }

  refreshLogin = () => {
    return (dispatch) => {
      const refreshToken = this.getRefreshToken();
      if (refreshToken) {
        this.loginRequest(dispatch, api.loginRefresh(refreshToken));
      } else {
        dispatch(this.loginPending(false));
        // history.replace(location); //rerun auth guards for routes
      }
    };
  };

  login = (email, password, rememberMe) => {
    return (dispatch) => {
      const validateEmail = /^(([^<>()[\]\\.,;:\s@]+(\.[^<>()[\]\\.,;:\s@]+)*)|(.+))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      const isEmailValid = validateEmail.test(email);
      if (isEmailValid) {
        this.loginRequest(dispatch, api.login({ email, password }), rememberMe);
      } else {
        dispatch(this.loginPending(false));
        dispatch(this.authSetError('Please enter valid email address'));
      }
    };
  };

  logout = () => {
    return (dispatch) => {
      this.clearRefreshToken();
      // dispatch(this.saveUserData({ token: '' }));
      dispatch(this.userLogout(true));
      dispatch(this.loginPending(false));
      // history.push('/auth');
    };
  };

  getRestrictions = () =>
    tokenInject((dispatch, getState, token) => {
      api
        .getRestrictions(token)
        .then((data) => {
          dispatch(this.setRestrictions(data));
        })
        .catch((errors) => {
          dispatch(addAlert(errors));
        });
    });

  handleErrors = () => (dispatch) => {
    $(document).ajaxError((event, jqXHR, settings, thrownError) => {
      if (jqXHR.status === 402) {
        const response = jqXHR.responseJSON;
        const failedRestriction = response.failedRestriction;
        const restrictions = response.restrictions;
        const limit = restrictions.limits[failedRestriction];
        if (limit) {
          dispatch(this.setRestrictions(restrictions));
          // dispatch({ type: `[Base] ${TOGGLE_UPGRADE_PLAN}`, payload: true }); // uncomment when upgrade page is ready
          dispatch(
            addAlert({
              type: 'error',
              transKey: 'restriction',
              id: 'restriction'
            })
          );
        }
      }
    });
  };

  defineActions() {
    this.loginPending = this.set(ACTIONS.PENDING, 'isAuthPending');
    this.saveUserData = this.merge(ACTIONS.SAVE_USER_DATA);
    this.authSetError = this.setIn(ACTIONS.SET_FORM_ERROR, ['form', 'error']);
    this.setRestrictions = this.mergeIn(ACTIONS.SET_RESTRICTIONS, [
      'user',
      'restrictions'
    ]);
    this.userLogout = this.set(USER_LOGOUT, 'userLogout');

    return {
      login: this.login,
      logout: this.logout,
      refreshLogin: this.refreshLogin,
      authSetError: this.authSetError,
      handleErrors: this.handleErrors,
      getRestrictions: this.getRestrictions
    };
  }
}

const auth = new Auth();
auth.init();

export const getRestrictions = auth.actions.getRestrictions;
export default auth;
