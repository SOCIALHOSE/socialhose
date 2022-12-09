import * as api from '../../../api/dashboardApi'
import ReduxModule from '../abstract/reduxModule'

export const LOAD_DASHBOARDS = 'Load dashboards'

class Dashboards extends ReduxModule {

  getNamespace () {
    return '[Dashboard]'
  }

  _loadDashboards ({token, fulfilled}) {
    return api
      .getDashboards(token)
      .then((dashboards) => {
        fulfilled(dashboards)
      })
  }

  defineActions () {
    const loadDashboards = this.thunkAction(LOAD_DASHBOARDS, this._loadDashboards)

    return {
      loadDashboards
    }
  }

  getInitialState () {
    return {
      dashboards: []
    }
  }

  defineReducers () {
    return {
      [LOAD_DASHBOARDS]: this.setReducer('dashboards')
    }
  }

}

const dashboards = new Dashboards()
dashboards.init()

export default dashboards
