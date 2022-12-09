import {createApi} from '../common/Common'

export const getSavedAnalysesApi = createApi('GET', '/api/v1/analytic', {
  inputData: (data) => {
    if (data.sort !== 'numberCharts') {
      data.sort = 's.' + data.sort
    }
    return data
  },
  rejectData: (defHandler, xhr) => {
    return defHandler(xhr, undefined, 'Cannot get saved analyses')
  }
})
export const deleteSavedAnalysesApi = createApi('DELETE', '/api/v1/analytic/delete', {
  inputData: (ids) => {
    return JSON.stringify({ids: ids})
  },
  rejectData: (defHandler, xhr) => {
    return defHandler(xhr, undefined, 'Cannot delete saved analyses')
  }
})
