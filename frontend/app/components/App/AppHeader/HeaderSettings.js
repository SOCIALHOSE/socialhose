import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import UserSettingsMenu from './UserSettingsMenu';
import { DropdownToggle, DropdownMenu, Dropdown } from 'reactstrap';
import { faAngleDown, faUser } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

export class HeaderSettings extends React.Component {
  static propTypes = {
    userFirstName: PropTypes.string.isRequired,
    userLastName: PropTypes.string.isRequired
  };

  state = {
    isOpen: false
  };

  toggleUserSettingsDrop = () => {
    this.setState((prev) => ({ isOpen: !prev.isOpen }));
  };

  render() {
    const { userFirstName, userLastName } = this.props;
    const isRTL = document.documentElement.dir === 'rtl';

    return (
      <Fragment>
        <div className="header-btn-lg pr-0">
          <div className="widget-content p-0">
            <div className="widget-content-wrapper">
              <div className="widget-content-left">
                <Dropdown
                  isOpen={this.state.isOpen}
                  toggle={this.toggleUserSettingsDrop}
                >
                  <DropdownToggle
                    color="link"
                    title="User Profile"
                    className="d-flex align-items-center p-0"
                    data-tour="app-header-user-settings"
                  >
                    <div className="user-profile">
                      <FontAwesomeIcon
                        className="user-profile-icon"
                        icon={faUser}
                      />
                    </div>
                    {window.outerWidth >= 768 && (
                      <FontAwesomeIcon
                        className="ml-2 opacity-8"
                        icon={faAngleDown}
                      />
                    )}
                  </DropdownToggle>
                  <DropdownMenu
                    className={`rm-pointers dropdown-menu-lg${
                      isRTL ? ' dropdown-menu-left' : ''
                    }`}
                  >
                    <UserSettingsMenu
                      toggleMenu={this.toggleUserSettingsDrop}
                      userFirstName={userFirstName}
                      userLastName={userLastName}
                    />
                  </DropdownMenu>
                </Dropdown>
              </div>
              <div className="widget-content-left ml-3 header-user-info">
                <div className="widget-heading">
                  {userFirstName + ' ' + userLastName}
                </div>
              </div>
            </div>
          </div>
        </div>
      </Fragment>
    );
  }
}

export default translate(['common'], { wait: true })(
  React.memo(HeaderSettings)
);
