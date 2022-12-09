import React from 'react'
import PropTypes from 'prop-types'
import Source from './Source'

export class Sources extends React.Component {
  static propTypes = {
    sources: PropTypes.array.isRequired,
    removeSource: PropTypes.func.isRequired,
    moveSource: PropTypes.func.isRequired    
  };

  render () {
    const { sources, removeSource, moveSource } = this.props

    return (
      <div>
        {sources.map((source, i) => {
          return (
            <Source
              key={'dragged-source-item-' + i}
              source={source}
              removeSource={removeSource}
              moveSource={moveSource}
            />
          )
        })}
      </div>
    )
  }

}

export default Sources
