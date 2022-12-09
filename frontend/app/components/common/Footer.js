import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';

function Footer({ t }) {
  return (
    <footer className="footer">
      <ul className="footer__list">
        <li className="footer__item">
          <a
            title={t('footer.privacyPolicy')}
            target="_blank"
            href="https://www.socialhose.io/en/legal/privacy"
            className="footer__link"
          >
            {t('footer.privacyPolicy')}
          </a>
        </li>
        <li className="footer__item">
          <a
            title={t('footer.acceptableUsePolicy')}
            target="_blank"
            href="https://www.socialhose.io/en/legal/acceptable-use"
            className="footer__link"
          >
            {t('footer.acceptableUsePolicy')}
          </a>
        </li>
        <li className="footer__item">
          <a
            title={t('footer.termsConditions')}
            target="_blank"
            href="https://www.socialhose.io/en/legal/terms"
            className="footer__link"
          >
            {t('footer.termsConditions')}
          </a>
        </li>
        <li className="footer__item">
          {t('footer.copyright', { year: new Date().getFullYear() })}
        </li>
      </ul>
    </footer>
  );
}

Footer.propTypes = {
  t: PropTypes.func.isRequired
};

export default translate(['loginApp'], { wait: true })(Footer);
