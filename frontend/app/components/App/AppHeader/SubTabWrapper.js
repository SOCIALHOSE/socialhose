import React from 'react';
import PropTypes from 'prop-types';
import { NavLink } from 'react-router-dom';
import { translate } from 'react-i18next';

class SubTabWrapper extends React.Component {
  static propTypes = {
    activeTabName: PropTypes.string.isRequired,
    subTabs: PropTypes.array.isRequired,
    t: PropTypes.func.isRequired,
    children: PropTypes.object
  };

  render() {
    const { t, activeTabName, subTabs, children } = this.props;

    return (
      <div className="rc-tabs-top position-relative" key="sub-tab-wrapper">
        <div role="tablist" className="rc-tabs-bar" tabIndex="0">
          <div className="rc-tabs-nav-container">
            <div className="rc-tabs-nav-wrap mask-line pt-0">
              <div className="rc-tabs-nav-scroll">
                <div className="rc-tabs-nav rc-tabs-nav-animated">
                  {subTabs &&
                    subTabs.map((subTab) => {
                      const tabText =
                        activeTabName === 'dashboard'
                          ? subTab.title
                          : t('tabs.' + subTab.title);
                      const fullUrl =
                        '/app/' + activeTabName + '/' + subTab.url;

                      return (
                        <NavLink
                          to={fullUrl}
                          key={subTab.url}
                          activeClassName="rc-tabs-tab-active rc-tabs-ink-bar rc-tabs-ink-bar-animated"
                          className="rc-tabs-tab"
                        >
                          {tabText}
                        </NavLink>
                      );
                    })}
                </div>
              </div>
            </div>
          </div>
        </div>
        {children}
      </div>
    );
  }
}

export default translate(['common'], { wait: true })(SubTabWrapper);
