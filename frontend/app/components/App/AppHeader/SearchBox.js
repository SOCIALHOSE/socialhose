import React, { Fragment } from 'react'

import cx from 'classnames'

class SearchBox extends React.Component {
  constructor (props) {
    super(props)

    this.state = {
      activeSearch: false
    }
  }

  activeSearchFunc = () => {
    this.setState({ activeSearch: !this.state.activeSearch })
  }

  render () {
    return (
      <Fragment>
        <div className={cx('search-wrapper', {
          active: this.state.activeSearch
        })}>
          <div className="input-holder">
            <input type="text" className="search-input" placeholder="Type to search" />
            <button onClick={this.activeSearchFunc}
              className="search-icon">
              <span />
            </button>
          </div>
          <button onClick={this.activeSearchFunc}
            className="close" />
        </div>
      </Fragment>
    )
  }
}

export default SearchBox
