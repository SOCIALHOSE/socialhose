import i18n from 'i18next';
// import XHR from 'i18next-xhr-backend';
import LanguageDetector from 'i18next-browser-languagedetector';
import resBundle from 'i18next-resource-store-loader!./locales/index.js';
import { isDevelopment } from './common/constants';

/*function loadLocales (url, options, callback, data) {
  try {
    let waitForLocale = require('bundle!./locales/' + url + '.json');
    waitForLocale((locale) => {
      callback(locale, {status: '200'});
    })
  } catch (e) {
    callback(null, {status: '404'});
  }
}*/

i18n
  // .use(XHR)
  .use(LanguageDetector)
  .init({
    fallbackLng: 'en',
    ns: ['common'],
    defaultNS: 'common',

    debug: isDevelopment,

    interpolation: {
      escapeValue: false, // not needed for react!!
      formatSeparator: ',',
      format: (value, format, lng) => {
        if (format === 'uppercase') return value.toUpperCase();
        return value;
      }
    },
    resources: resBundle,
    /*backend: {
    loadPath: '{{lng}}/{{ns}}',
    parse: (data) => data,
    ajax: loadLocales
    },*/
    detection: {
      order: ['localStorage', 'cookie', 'navigator', 'querystring', 'htmlTag']
    }
  });

export default i18n;
