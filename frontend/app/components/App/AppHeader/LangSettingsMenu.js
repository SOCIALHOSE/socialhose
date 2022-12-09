import React from 'react';
import PropTypes from 'prop-types';
import { compose } from 'redux';
import { translate } from 'react-i18next';
import i18n from '../../../i18n';
import {
  UncontrolledDropdown,
  DropdownToggle,
  DropdownMenu,
  DropdownItem
} from 'reactstrap';
import reduxConnect from '../../../redux/utils/connect';

import Flag from 'react-flagkit';

const langCountry = {
  en: 'US',
  ar: 'SA',
  fr: 'FR'
};

function LangSettingsMenu(props) {
  const {
    t,
    base: { langs, activeLang },
    actions,
    direction = ''
  } = props;

  const chooseLang = (e) => {
    const newLang = e.target.dataset.lang;
    actions.chooseLanguage(newLang);
    i18n.changeLanguage(newLang);
  };
  const isRTL = document.documentElement.dir === 'rtl';

  const dropDownProps = {};
  if (direction) {
    dropDownProps.direction = direction;
  }

  return (
    <UncontrolledDropdown {...dropDownProps}>
      <DropdownToggle className="p-0 mr-2" color="link">
        <div className="icon-wrapper icon-wrapper-alt rounded-circle">
          <div className="icon-wrapper-bg bg-focus" />
          <div className="language-icon">
            <Flag
              className="mr-3 opacity-8"
              country={langCountry[activeLang]}
              size="40"
            />
          </div>
        </div>
      </DropdownToggle>
      <DropdownMenu
        className={`rm-pointers${isRTL ? ' dropdown-menu-left' : ''}`}
      >
        <div className="dropdown-menu-header">
          <div className="dropdown-menu-header-inner pt-4 pb-4 bg-focus">
            <div className="menu-header-content text-center text-white">
              <h6 className="menu-header-subtitle mt-0">
                {t('langs.chooseLanguage')}
              </h6>
            </div>
          </div>
        </div>

        {langs.map((lang, i) => {
          const translateTarget = 'langs.' + lang;
          return (
            <DropdownItem
              key={lang}
              active={activeLang === lang}
              data-lang={lang}
              onClick={chooseLang}
            >
              <Flag className="mr-3 opacity-8" country={langCountry[lang]} />
              {t(translateTarget)}
            </DropdownItem>
          );
        })}
      </DropdownMenu>
    </UncontrolledDropdown>
  );
}

LangSettingsMenu.propTypes = {
  t: PropTypes.func.isRequired,
  base: PropTypes.object.isRequired,
  actions: PropTypes.object.isRequired,
  direction: PropTypes.string
};

const applyDecorators = compose(
  reduxConnect('base', ['common', 'base']),
  translate(['common'], { wait: true })
);

export default applyDecorators(LangSettingsMenu);
