import axios from 'axios';
import { cloneDeep } from 'lodash';
import appConfig from '../../appConfig';
import { hubspotBaseURL } from '../../common/constants';
import { getHPContext } from '../../common/helper';
import {
  get,
  handleError,
  handleResponse,
  post
} from '../httpInterceptor/httpInterceptor';

export const cancelPlan = async () => {
  let url = '/users/cancel/plan';
  const res = await post(url);
  console.log('API Response :: cancelPlan ::: ', res);
  return res;
};

export const getTransactions = async (params) => {
  let url = '/users/invoices';
  const res = await get(url, params);
  console.log('API Response :: getTransactions ::: ', res);
  return res;
};

export const updatePlanPayment = async (data) => {
  let url = '/users/update/plan';
  const res = await post(url, data);
  console.log('API Response :: updatePlanPayment ::: ', res);
  return res;
};

export const changeCardDetails = async (data) => {
  let url = '/users/card/change';
  const res = await post(url, data);
  console.log('API Response :: changeCard ::: ', res);
  return res;
};

// submit update plan data to Hubspot form API
export const updatePlanHubspot = (dataObj) => {
  const { hubSpotportalID } = appConfig;
  if (!hubSpotportalID) {
    return Promise.resolve('No IDs');
  }

  const data = cloneDeep(dataObj);
  data.line1 = data.line2 ? [data.line1, data.line2].join(', ') : data.line1;
  const hubSpotFormURL = `${hubspotBaseURL}/47b0e83d-0e26-4528-8822-9aec64db35e8`;
  const hubSpotMapping = {
    savedFeeds: 'feed_licenses',
    searchesPerDay: 'search_licenses',
    webFeeds: 'webfeed_licenses',
    alerts: 'alert_licenses',
    subscriberAccounts: 'user_accounts',
    line1: 'address',
    city: 'city',
    state: 'state',
    postal_code: 'zip',
    country: 'country',
    phone: 'phone',
    email: 'email',
    totalCost: 'amount'
  };

  const mediaTypesMapping = {
    news: 'News',
    blog: 'Blogs',
    reddit: 'Reddit',
    twitter: 'Twitter',
    instagram: 'Instagram'
  };

  const mediaTypes = Object.keys(mediaTypesMapping)
    .filter((key) => data[key])
    .map((v) => mediaTypesMapping[v])
    .join(';');

  const newObj = Object.keys(hubSpotMapping)
    .filter((key) => data[key])
    .map((key) => ({
      name: hubSpotMapping[key],
      value: data[key]
    }));

  newObj.push({
    name: 'media_types',
    value: mediaTypes
  });

  newObj.push({
    name: 'analytics',
    value: data['analytics'] && data['analytics'] !== 0
  });

  return axios
    .post(hubSpotFormURL, {
      fields: newObj,
      context: getHPContext()
    })
    .then((response) => handleResponse(response))
    .catch((error) => handleError(error));
};

// submit cancel plan data to Hubspot form API
export const cancelPlanHubspot = (dataObj) => {
  const { hubSpotportalID } = appConfig;
  if (!hubSpotportalID) {
    return Promise.resolve('No IDs');
  }

  const data = cloneDeep(dataObj);
  const hubSpotFormURL = `${hubspotBaseURL}/4d2496c3-0535-4723-8b5e-bd04e7903338`;
  const hubSpotMapping = {
    email: 'email',
    content: 'TICKET.content',
    subject: 'TICKET.subject'
  };

  const reason = {
    1: '1',
    2: '2',
    3: '3',
    4: '4',
    5: '5',
    Other: 'Other'
  };

  const reasonValues = Object.keys(reason)
    .filter((key) => data[key])
    .map((v) => reason[v])
    .join(';');

  const newObj = Object.keys(hubSpotMapping)
    .filter((key) => data[key])
    .map((key) => ({
      name: hubSpotMapping[key],
      value: data[key]
    }));

  newObj.push({
    name: 'cancelreason',
    value: reasonValues
  });

  return axios
    .post(hubSpotFormURL, {
      fields: newObj,
      context: getHPContext()
    })
    .then((response) => handleResponse(response))
    .catch((error) => handleError(error));
};
