import PropTypes from 'prop-types'
import React, { Fragment } from 'react'

import { Slider } from 'react-burgers'

import { compose } from 'redux'
import reduxConnect from '../../../redux/utils/connect'
import translate from 'react-i18next/dist/commonjs/translate'
import AppMobileMenu from './AppMobileMenu'

class HeaderLogo extends React.Component {
  constructor (props) {
    super(props)
    this.state = {
      active: false,
      mobile: false,
      activeSecondaryMenuMobile: false
    }
  }

  toggleEnableClosedSidebar = () => {
    const { setEnableClosedSidebar } = this.props.actions
    const { enableClosedSidebar } = this.props.appState.themeOptions
    setEnableClosedSidebar(!enableClosedSidebar)
  }

  state = {
    openLeft: false,
    openRight: false,
    relativeWidth: false,
    width: 280,
    noTouchOpen: false,
    noTouchClose: false
  }

  changeActive = () => {
    this.setState({ active: !this.state.active })
  }

  render () {
    return (
      <Fragment>
        <div className="app-header__logo">
          <div className="logo-src ml-0 ml-lg-2" />
          <div className="header__pane ml-auto">
            <div onClick={this.toggleEnableClosedSidebar}>
              <Slider
                width={26}
                lineHeight={2}
                lineSpacing={5}
                color="#6c757d"
                active={this.state.active}
                onClick={this.changeActive}
              />
            </div>
          </div>
        </div>
        <AppMobileMenu />
      </Fragment>
    )
  }
}

HeaderLogo.propTypes = {
  actions: PropTypes.object,
  appState: PropTypes.object
}

const applyDecorators = compose(
  reduxConnect('appState', ['appState']),
  translate(['tabsContent'], { wait: true })
)

export default applyDecorators(HeaderLogo)
