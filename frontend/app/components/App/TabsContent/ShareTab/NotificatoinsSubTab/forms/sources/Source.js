import React from 'react'
import PropTypes from 'prop-types'
import classnames from 'classnames'
import {
  IoIosArrowDropup,
  IoIosArrowDropdown,
  IoIosCloseCircleOutline
} from 'react-icons/io'

export class Source extends React.Component {
  static propTypes = {
    source: PropTypes.object.isRequired,
    removeSource: PropTypes.func.isRequired,
    moveSource: PropTypes.func.isRequired
  }

  onRemove = () => {
    const { source, removeSource } = this.props
    removeSource(source.id)
  }

  onMoveUp = () => {
    const { source, moveSource } = this.props
    moveSource(source.id, true)
  }

  onMoveDown = () => {
    const { source, moveSource } = this.props
    moveSource(source.id, false)
  }

  render() {
    const { source } = this.props

    return (
      <div className="d-flex mr-2 mb-2">
        <p
          className={classnames(
            'd-flex align-items-center feed-icon',
            source.class
          )}
        >
          {source.name}
        </p>
        <div className="ml-sm-4">
          <button
            title="Up"
            type="button"
            className="btn p-0"
            onClick={this.onMoveUp}
          >
            <IoIosArrowDropup size={22} className="text-secondary ml-2" />
          </button>
          <button
            title="Down"
            type="button"
            className="btn p-0"
            onClick={this.onMoveDown}
          >
            <IoIosArrowDropdown size={22} className="text-secondary ml-2" />
          </button>
          <button
            title="Remove"
            type="button"
            className="btn p-0"
            onClick={this.onRemove}
          >
            <IoIosCloseCircleOutline size={22} className="text-danger ml-2" />
          </button>
        </div>
      </div>
    )
  }
}

export default Source
