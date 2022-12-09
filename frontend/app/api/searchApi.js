import axios from 'axios'
import { cloneDeep } from 'lodash'
import appConfig from '../appConfig'
import {createApi} from '../common/Common'
import { hubspotBaseURL } from '../common/constants'
import { getHPContext } from '../common/helper'
import { handleError, handleResponse } from './httpInterceptor/httpInterceptor'

const slRoot = '/api/v1/source-list'

export const searchQuery = createApi('POST', '/api/v1/query/search', {})

export const searchSources = createApi('POST', '/api/v1/source-index/', {})

export const addSourcesToLists = createApi('POST', '/api/v1/source-index/add-to-sources-list', {})

export const replaceSourceListsForSource = createApi('POST', '/api/v1/source-index/{id}/list', {
  urlData: (params) => ({id: params.id}),
  inputData: (params) => JSON.stringify({sourceLists: params.sourceLists})
})

export const getSourceLists = createApi('POST', `${slRoot}/list`, {})

export const addSourceLists = createApi('POST', `${slRoot}/`, {
  inputData: (name) => JSON.stringify({name})
})

export const renameSourceLists = createApi('PUT', `${slRoot}/{id}`, {
  urlData: (params) => ({id: params.id}),
  inputData: (params) => JSON.stringify({name: params.name})
})

export const cloneSourceLists = createApi('POST', `${slRoot}/{id}/clone`, {
  urlData: (params) => ({id: params.id}),
  inputData: (params) => JSON.stringify({name: params.name})
})

export const deleteSourceLists = createApi('DELETE', `${slRoot}/{id}`, {
  urlData: (id) => ({id}),
  inputData: () => {}
})

export const getSourcesOfList = createApi('POST', `${slRoot}/{id}/sources/search`, {
  urlData: (data, id) => ({id})
})

export const shareSourceList = createApi('POST', `${slRoot}/{id}/share`, {
  urlData: (id) => ({id}),
  inputData: () => null
})
export const unshareSourceList = createApi('POST', `${slRoot}/{id}/unshare`, {
  urlData: (id) => ({id}),
  inputData: () => null
})

// submit search queries to Hubspot form API for free user
export const submitSearchHubspot = (dataObj) => {
  const { hubSpotportalID } = appConfig;
  if (!hubSpotportalID) {
    return Promise.resolve('No IDs');
  }

  const data = cloneDeep(dataObj);
  const hubSpotFormURL = `${hubspotBaseURL}/3f297902-d32d-44bb-89a6-12af1c7b886e`;
  const hubSpotMapping = {
    email: 'email',
    searchquery: 'searchquery'
    // raw_query: 'raw_query'
  };

  const newObj = Object.keys(hubSpotMapping)
    .filter((key) => data[key])
    .map((key) => ({
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
