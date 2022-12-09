import React from 'react'
import PropTypes from 'prop-types'
import { TYPES } from '../../../../../../redux/modules/appState/sidebar'
import { Interpolate } from 'react-i18next'
import { DragSource } from 'react-dnd'

const source = {
  beginDrag (props, monitor, component) {
    setTimeout(() => {
      component.setState({
        isDragging: true
      })
    }, 0)
    return {
      type: TYPES.CLIP_ARTICLE
    }
  },

  endDrag (props, monitor, component) {
    component.setState({
      isDragging: false
    })
  }
}

/**
 * Specifies which props to inject into component from Drag n Drop.
 */
function collect (connect) {
  return {
    // Call this function inside render()
    // to let React DnD handle the drag events:
    connectDragSource: connect.dragSource()
  }
}

export class ClipDragSource extends React.Component {

  static propTypes = {
    articles: PropTypes.array.isRequired,
    connectDragSource: PropTypes.func.isRequired
  };

  constructor (props) {
    super(props)
    this.state = {
      isDragging: false
    }
  }

  render () {

    const style = {
      visibility: this.state.isDragging ? 'hidden' : 'visible'
    }

    return this.props.connectDragSource(
      <div className="draggable-item" style={style}>
        <span className="drag-handle" />
        <Interpolate
          i18nKey='searchTab.clipPopup.clippedArticles'
          count={this.props.articles.length}
        />
      </div>
    )
  }
}

export default DragSource(TYPES.CLIP_ARTICLE, source, collect)(ClipDragSource)
