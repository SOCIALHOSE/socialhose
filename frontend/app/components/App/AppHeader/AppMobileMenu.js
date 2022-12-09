/* eslint-disable react/jsx-no-bind */
import PropTypes from 'prop-types';
import React, { Fragment } from 'react';
import { Slider } from 'react-burgers';
import cx from 'classnames';
import { faEllipsisV } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Button } from 'reactstrap';
import reduxConnect from '../../../redux/utils/connect';
import translate from 'react-i18next/dist/commonjs/translate';
import { compose } from 'redux';

class AppMobileMenu extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      active: false,
      mobile: false,
      activeSecondaryMenuMobile: false
    };
  }

  toggleMobileSidebar = () => {
    const { setEnableMobileMenu } = this.props.actions;
    const { enableMobileMenu } = this.props.appState.themeOptions;
    setEnableMobileMenu(!enableMobileMenu);
  };

  toggleMobileSmall = () => {
    const { setEnableMobileMenuSmall } = this.props.actions;
    const { enableMobileMenuSmall } = this.props.appState.themeOptions;
    setEnableMobileMenuSmall(!enableMobileMenuSmall);
  };

  state = {
    openLeft: false,
    openRight: false,
    relativeWidth: false,
    width: 280,
    noTouchOpen: false,
    noTouchClose: false
  };

  changeActive = () => {
    this.setState({ active: !this.state.active });
  };

  render() {
    return (
      <Fragment>
        <div className="app-header__mobile-menu" data-tour="mobile-left-menu">
          <div onClick={this.toggleMobileSidebar}>
            <Slider
              width={26}
              lineHeight={2}
              lineSpacing={5}
              color="#6c757d"
              active={this.state.active}
              onClick={this.changeActive}
            />
          </div>
        </div>
        <div className="app-header__menu">
          <span onClick={this.toggleMobileSmall}>
            <Button
              size="sm"
              className={cx('btn-icon btn-icon-only', {
                active: this.state.activeSecondaryMenuMobile
              })}
              color="primary"
              onClick={() =>
                this.setState({
                  activeSecondaryMenuMobile: !this.state
                    .activeSecondaryMenuMobile
                })
              }
            >
              <div className="btn-icon-wrapper">
                <FontAwesomeIcon icon={faEllipsisV} />
              </div>
            </Button>
          </span>
        </div>
      </Fragment>
    );
  }
}

AppMobileMenu.propTypes = {
  actions: PropTypes.object,
  appState: PropTypes.object
};

const applyDecorators = compose(
  reduxConnect('appState', ['appState']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(AppMobileMenu);
