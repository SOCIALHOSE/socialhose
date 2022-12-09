import * as api from '../../../../../api/themesApi'
import ReduxModule from '../../../abstract/reduxModule'

const GET_DEFAULT_THEME = 'Get default theme'

export class Themes extends ReduxModule {

  getNamespace () {
    return '[Themes]'
  }

  getDefaultTheme = ({token, fulfilled}) => {
    return api
      .getDefaultItem(token)
      .then((data) => {
        fulfilled(data)
        return data
      })
  };

  defineActions () {
    const getDefaultTheme = this.thunkAction(GET_DEFAULT_THEME, this.getDefaultTheme)
    return {
      getDefaultTheme
    }
  }

  getInitialState () {
    return {
      isPending: false,
      themes: [],
      defaultTheme: null,
      activeId: ''
    }
  }

  defineReducers () {
    return {
      [`${GET_DEFAULT_THEME} fulfilled`]: this.setReducer('defaultTheme')
    }
  }

}

const themes = new Themes()
themes.init()
export default themes

export const getDefaultTheme = themes.actions.getDefaultTheme
