import axios from 'axios';
import appConfig from '../../appConfig';
import {
  get,
  handleError,
  handleResponse,
  post
} from '../httpInterceptor/httpInterceptor';
import { getHPContext } from '../../common/helper';
import { hubspotBaseURL } from '../../common/constants';

export const getPlans = async () => {
  const url = '/security/plans';
  const res = await get(url, null, true, null, true);
  console.log('API Response :: getPlans ::: ', res);
  return res;
};

export const updatePrice = async (data) => {
  let url = '/security/cost_calculation';
  const res = await post(url, data, true, null, true);
  console.log('API Response :: updatePrice ::: ', res);
  return res;
};

export const registerUser = async (data) => {
  let url = '/security/registration';
  const res = await post(url, data, true, null, true);
  console.log('API Response :: registerUser ::: ', res);
  return res;
};

export const activeAccount = async (token) => {
  let url = `/security/registration/confirm/${token}`;
  const res = await post(url, undefined, true, null, true);
  console.log('API Response :: activeAccount ::: ', res);
  return res;
};

// submit data for form API
export const submitHubspot = (data) => {
  const { hubSpotportalID } = appConfig;
  if (!hubSpotportalID) {
    return Promise.resolve('No IDs');
  }

  const hubSpotFormURL = `${hubspotBaseURL}/070e31d4-8e6d-480d-89b2-872a6bb28ff4`;
  const hubSpotMapping = {
    email: 'email',
    firstName: 'firstname',
    lastName: 'lastname',
    companyName: 'company',
    jobFunction: 'job_function',
    numberOfEmployee: 'numemployees',
    industry: 'industry',
    websiteUrl: 'website',
    lifecyclestage: 'lifecyclestage'
  };

  const newObj = Object.keys(hubSpotMapping).map((key) => ({
    name: hubSpotMapping[key],
    value: data[key]
  }));

  return axios
    .post(hubSpotFormURL, {
      fields: newObj,
      context: getHPContext()
    })
    .then((response) => handleResponse(response))
    .catch((error) => handleError(error));
};
