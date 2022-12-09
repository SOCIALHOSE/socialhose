import axios from 'axios';
import apiBase from '../../appConfig';
import i18n from '../../i18n';

export const get = (
  url,
  params,
  isPublic = false,
  responseType = null,
  passedFullURL = false
) => {
  let apiUrl = passedFullURL
    ? `${apiBase.apiUrl}${url}`
    : `${apiBase.apiUrl}/api/v1${url}`;

  const axiosInstance = axios.create();

  const axiosObj = {
    method: 'get',
    url: apiUrl,
    params: params
  };

  if (isPublic) {
    // apis in which no authentication needed
    axiosInstance.transformRequest = (data, headers) => {
      delete headers.common['Authorization'];
    };
  }

  if (responseType) axiosObj.responseType = responseType;
  return axiosInstance(axiosObj)
    .then((response) => handleResponse(response))
    .catch((error) => handleError(error));
};

export function put(...rest) {
  return dataRequest('put', ...rest);
}

export function post(...rest) {
  return dataRequest('post', ...rest);
}

export function del(...rest) {
  return dataRequest('delete', ...rest);
}

const dataRequest = (
  type = 'post',
  url,
  bodyObj = undefined,
  isPublic = false,
  mediaFile = false,
  passedFullURL = false
) => {
  const apiUrl = passedFullURL
    ? `${apiBase.apiUrl}${url}`
    : `${apiBase.apiUrl}/api/v1${url}`;

  if (mediaFile) {
    const formData = new FormData();
    Object.keys(bodyObj).map((key) => {
      formData.append(key, bodyObj[key]);
    });
    bodyObj = formData;
  }

  const axiosInstance = axios.create();

  const axiosObj = {
    method: type,
    url: apiUrl,
    data: bodyObj
  };

  if (isPublic) {
    // apis in which no authentication needed
    axiosInstance.transformRequest = (data, headers) => {
      delete headers.common['Authorization'];
    };
  }

  return axiosInstance(axiosObj)
    .then((response) => handleResponse(response))
    .catch((error) => handleError(error));
};

export const handleResponse = (response) => {
  if (
    response.data &&
    (response.data.code === 403 || response.data.code === 404)
  ) {
    return {
      error: true,
      errorMessage: response.data.message,
      data: response.message
    };
  }
  return {
    error: false,
    data: response.data
  };
};

export const handleError = (error) => {
  const { response } = error;
  let errorMsg = i18n.t('common:alerts.error.somethingWrong');
  if (response && response.status === 422) {
    if (response.data.message) errorMsg = response.data.message;
  } else if (response && response.status === 401) {
    // Unauthorized
  }
  console.log('API Error ::: ', JSON.stringify(response));

  return {
    error: true,
    errorMessage: errorMsg,
    data: response ? response.data.errors : null,
    status: response ? response.status : null
  };
};
