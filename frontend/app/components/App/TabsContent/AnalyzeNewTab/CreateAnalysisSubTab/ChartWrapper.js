import React from 'react';
import PropTypes from 'prop-types';
import {
  Card,
  CardBody,
  CardHeader,
  DropdownItem,
  DropdownMenu,
  DropdownToggle,
  UncontrolledButtonDropdown
} from 'reactstrap';
import cx from 'classnames';
import { IoIosMenu } from 'react-icons/io';

function ChartWrapper(props) {
  let { title, children, menus } = props;

  const hasShowMore = menus.find((menu) => !menu.hide && menu.showInMore);

  // TODO: hide alert until API is ready
  menus = menus.filter((menu) => menu.title);

  const isRTL = document.documentElement.dir === 'rtl';
  return (
    <Card className="mb-3">
      <CardHeader>
        {title && <div>{title}</div>}
        <div className="btn-actions-pane-right actions-icon-btn">
          <div className="align-content-center d-flex d-inline-flex">
            {menus &&
              menus.map((menu) =>
                !menu.hide && !menu.showInMore && menu.icon ? (
                  <button
                    key={menu.title}
                    title={menu.title}
                    className="btn btn-icon-only mr-2 p-0"
                    onClick={menu.fn}
                    disabled={!menu.fn}
                  >
                    <menu.icon size={menu.size || 16} />
                  </button>
                ) : null
              )}
          </div>
          {menus && hasShowMore && (
            <UncontrolledButtonDropdown>
              <DropdownToggle className="btn-icon btn-icon-only" color="link">
                <div className="btn-icon-wrapper">
                  <IoIosMenu size={24} />
                </div>
              </DropdownToggle>
              <DropdownMenu
                className={`dropdown-menu-shadow dropdown-menu-hover-link${
                  isRTL ? ' dropdown-menu-left' : ''
                }`}
              >
                {menus.map((menu) =>
                  !menu.hide && menu.showInMore ? (
                    <DropdownItem onClick={menu.fn} key={menu.title}>
                      {menu.icon && (
                        <i className={cx('dropdown-icon', menu.icon)}></i>
                      )}
                      <span>{menu.title}</span>
                    </DropdownItem>
                  ) : null
                )}
              </DropdownMenu>
            </UncontrolledButtonDropdown>
          )}
        </div>
      </CardHeader>
      <CardBody>{children}</CardBody>
    </Card>
  );
}

ChartWrapper.propTypes = {
  title: PropTypes.string,
  children: PropTypes.oneOfType([PropTypes.object, PropTypes.string]),
  menus: PropTypes.array
};

export default ChartWrapper;
