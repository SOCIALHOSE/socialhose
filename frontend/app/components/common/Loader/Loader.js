import React, { Component } from 'react'

export default class LoadersAdvanced extends Component {
  render () {
    return (
      <div className="out-space">
        <div className="lds-ellipsis">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>
      </div>
    )
  }
}
