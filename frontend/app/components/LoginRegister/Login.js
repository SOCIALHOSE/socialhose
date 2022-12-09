import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { compose } from 'redux';
import reduxConnect from '../../redux/utils/connect';
import { Link } from 'react-router-dom';
import { Col, Row, Button, Form, FormGroup, Label, Input } from 'reactstrap';

import CommonSection from './CommonSection';
import { setDocumentData } from '../../common/helper';
import { isLive } from '../../common/constants';
import LangSettingsMenu from '../App/AppHeader/LangSettingsMenu';

class Login extends React.Component {
  static propTypes = {
    store: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  constructor() {
    super();
    this.state = {
      email: '',
      password: ''
    };
  }

  componentDidMount() {
    setDocumentData('title', 'Login');
  }

  componentWillUnmount() {
    this.props.actions.authSetError('');
    setDocumentData('title');
  }

  submitHandler = (e) => {
    e.preventDefault();
    const { email, password } = this.state;
    this.props.actions.login(email, password);
  };

  changeHandler = ({ target: { name, value } }) => {
    this.setState({ [name]: value });
  };

  render() {
    const { t, store } = this.props;
    const loginError = store.common.auth.form.error;

    return (
      <div className="vh-100">
        <Row className="vh-100 no-gutters">
          <CommonSection />
          <Col
            lg="8"
            md="12"
            className="vh-100 d-flex bg-white justify-content-center align-items-center position-relative"
          >
            <Col lg="9" md="10" sm="12" className="mx-auto app-login-box">
              <div className="app-logo" />
              <h4>
                <div>{t('login.mainLabel')}</div>
                <span>{t('login.subLabel')}</span>
              </h4>
              <h6 className="mt-3">
                {t('login.noAccount')}{' '}
                {isLive ? (
                  <a
                    className="text-primary"
                    href="https://landing.socialhose.io/en/signup"
                    rel="noopener noreferrer"
                  >
                    {t('login.signUpNow')}
                  </a>
                ) : (
                  <Link className="text-primary" to="/auth/register">
                    {t('login.signUpNow')}
                  </Link>
                )}
              </h6>
              <Row className="divider" />
              <div>
                <Form onSubmit={this.submitHandler}>
                  <Row form>
                    <Col md={6}>
                      <FormGroup>
                        <Label for="exampleEmail">
                          {t('login.form.emailLabel')}
                        </Label>
                        <Input
                          type="email"
                          name="email"
                          value={this.state.email}
                          onChange={this.changeHandler}
                          placeholder={t('login.form.emailPlaceholder')}
                        />
                      </FormGroup>
                    </Col>
                    <Col md={6}>
                      <FormGroup>
                        <Label for="examplePassword">
                          {t('login.form.passwordLabel')}
                        </Label>
                        <Input
                          type="password"
                          name="password"
                          value={this.state.password}
                          onChange={this.changeHandler}
                          placeholder={t('login.form.passwordPlaceholder')}
                        />
                      </FormGroup>
                    </Col>
                    <Col md={12}>
                      {loginError && (
                        <p className="text-danger mb-3">{loginError}</p>
                      )}
                    </Col>
                  </Row>
                  <Row className="divider" />
                  <div className="d-flex align-items-center">
                    <div className="ml-auto">
                      <Link
                        to="/auth/forgot-password"
                        className="btn-lg btn btn-link"
                      >
                        {t('login.forgotPass')}
                      </Link>{' '}
                      <Button type="submit" color="primary" size="lg">
                        {t('login.signInBtn')}
                      </Button>
                    </div>
                  </div>
                </Form>
              </div>
            </Col>
            <div className="header-dots public-lang">
              <LangSettingsMenu direction="left" />
            </div>
          </Col>
        </Row>
      </div>
    );
  }
}

const applyDecorators = compose(
  reduxConnect(),
  translate(['loginApp'], { wait: true })
);

export default applyDecorators(Login);
