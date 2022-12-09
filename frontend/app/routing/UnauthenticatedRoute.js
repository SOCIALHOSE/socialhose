import React, { useEffect } from 'react';
import { Redirect, Switch, Route } from 'react-router-dom';
import PropTypes from 'prop-types';
import ForgotPassword from '../components/LoginRegister/ForgotPassword';
import Login from '../components/LoginRegister/Login';
import ResetPassword from '../components/LoginRegister/ResetPassword';
import reduxConnect from '../redux/utils/connect';
// import Register from '../components/LoginRegister/Registration/Register';
import RegisterSuccess from '../components/LoginRegister/Registration/RegisterSuccess';
import RegisterConfirmEmail from '../components/LoginRegister/Registration/RegisterConfirmEmail';
import RegisterFreeAccount from '../components/LoginRegister/Registration/RegisterFreeAccount';
// import CostCalculator from '../components/LoginRegister/Registration/CostCalculator';

function UnauthenticatedRoute(props) {
  const { auth, history } = props;
  const { isAuthPending, token: isLoggedIn } = auth;

  useEffect(() => {
    if (!isAuthPending && isLoggedIn) {
      history.push('/app/search/search');
      return;
    }
  }, [isAuthPending, isLoggedIn]);

  if (!isAuthPending && isLoggedIn) {
    return null;
  }

  return (
    <Switch>
      <Route path="/auth/login" component={Login} />
      <Route path="/auth/register/:step?" component={RegisterFreeAccount} />
      {/* <Route path="/auth/register/:step?" component={Register} /> */}
      <Route path="/auth/register-success" component={RegisterSuccess} />
      <Route
        path="/auth/confirm-account/:token"
        component={RegisterConfirmEmail}
      />
      <Route path="/auth/forgot-password" component={ForgotPassword} />
      <Route path="/auth/reset-password" component={ResetPassword} />
      {/* <Route path="/cost-calculator" component={CostCalculator} /> */}
      <Redirect to="/auth/login" />
    </Switch>
  );
}

UnauthenticatedRoute.propTypes = {
  auth: PropTypes.object.isRequired,
  match: PropTypes.object.isRequired,
  history: PropTypes.object.isRequired
};

export default reduxConnect('auth', ['common', 'auth'])(UnauthenticatedRoute);
