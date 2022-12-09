import moment from 'moment';
import { cloneDeep } from 'lodash';
import axios from 'axios';
import Cookies from 'cookies-js';

// append scripts in body
export const appendScriptLink = (sources) => {
  sources.map((src) => {
    const script = document.createElement('script');
    script.type = 'text/javascript';
    script.setAttribute('src', src);
    script.setAttribute('async', true);
    document.body.appendChild(script);
  });
};

// load script into body part
export const loadScript = (source) => {
  const s = document.createElement('script');
  s.type = 'text/javascript';
  s.async = true;
  s.innerHTML = source;
  document.body.appendChild(s);
};

// set document element value
export const setDocumentData = (tag, value) => {
  if (tag === 'title') {
    document.title = value
      ? `${value} | SOCIALHOSE.IO App`
      : 'Social Listening Platform | Social Analytics | SOCIALHOSE.IO App';
  }
};

// convert UTC date to Local date
export const convertUTCtoLocal = (date, format = 'YYYY-MM-DD HH:mm:ss') => {
  if (!date) return '';
  const utcDate = moment.utc(date).format(); //is used to consider input as UTC if timezone offset is not passed
  return moment(utcDate).format(format);
};

// convert Local date to UTC date
export const convertlocaltoUTC = (date, format = 'YYYY-MM-DD HH:mm:ss') => {
  if (!date) return '';
  return moment.utc(date).format(format);
};

// get date
export const getDate = (date, format = 'YYYY-MM-DD HH:mm:ss') => {
  return !date ? moment().format(format) : moment(date).format(format);
};

export const getMomentObject = (date) => {
  return date ? (moment.isMoment(date) ? date : moment(date)) : null;
};

export const getQueryParams = (obj) => {
  if (!obj) {
    return null;
  }
  const { page, pageSize = 10, sorted, searchQuery = undefined } = obj;
  const params = {
    page: page + 1,
    limit: pageSize,
    query: searchQuery
  };
  if (sorted && sorted.length) {
    const sortedField = sorted[0];
    const sort = {
      field: sortedField.id,
      direction: sortedField.desc ? 'desc' : 'asc'
    };
    params['sort'] = sort;
  }
  return params;
};

export function removeHttpsUrl(url) {
  return !url ? '' : url.replace(/(^\w+:|^)\/\//, '');
}

export function capOnlyFirstLetter(string) {
  // lodash: capitalize
  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

export function capFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

export function getValidHttpUrl(string) {
  let url;

  try {
    url = new URL(string);
  } catch (_) {
    return false;
  }

  url.protocol = 'https:';
  return url.toString();
}

export function getArray(obj, key = 'name', value = 'value') {
  return Object.entries(obj).map((v) => ({
    [key]: v[0],
    [value]: v[1]
  }));
}

// get title for source index table
export function getTitle(prevTitle) {
  if (prevTitle && prevTitle.replace(/!+/g, '').trim().length > 0) {
    return prevTitle;
  }

  return '[No Name]';
}

export function abbreviateNumber(num) {
  if (num >= 1000000000) {
    return (num / 1000000000).toFixed(1).replace(/\.0$/, '') + 'G';
  }
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
  }
  if (num >= 1000) {
    return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
  }
  return num;
}

export function notNullAndUnd(value) {
  return value !== null && value !== undefined;
}

export function validateForm(form, errors, handleValidation) {
  let failed;
  for (let val in errors) {
    const fieldError = errors[val];
    if (fieldError) {
      failed = true;
    } else if (fieldError === null && !form[val] && form[val] !== 0) {
      failed = true;
      handleValidation(val, true);
    }
  }
  if (failed) {
    return false;
  } else {
    return cloneDeep(form);
  }
}

// get IP
export function getIP() {
  return localStorage.getItem('ip');
}

export const setIP = async () => {
  try {
    const res = await axios.get('https://api.ipify.org/?format=json');
    res.data && res.data.ip && localStorage.setItem('ip', res.data.ip);
  } catch (error) {
    console.log(error);
  }
};

export function getHPContext() {
  return {
    hutk: Cookies.get('hubspotutk') || undefined,
    ipAddress: getIP() || undefined
  };
}

export function arraymove(arr, fromIndex, toIndex) {
  if (fromIndex === -1 || toIndex === -1) {
    return;
  }
  var element = arr[fromIndex];
  arr.splice(fromIndex, 1);
  arr.splice(toIndex, 0, element);
}
