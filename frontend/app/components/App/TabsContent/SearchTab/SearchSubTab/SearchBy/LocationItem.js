import React from 'react'
import PropTypes from 'prop-types'
import { DragSource } from 'react-dnd'

const Types = {
  LOC: 'location'
}

const locationSource = {
  beginDrag (props) {
    // Return the data describing the dragged item
    return { oldDropTargetType: props.dropTargetType }
  },

  endDrag (props, monitor, component) {
    // When dropped on a compatible target, do something
    if (monitor.getDropResult() !== null) {
      const locFrom = props.dropTargetType
      const locTo = monitor.getDropResult().newDropTargetType

      const locationType = props.locationType
      const location = props.location

      props.moveLocation(locFrom, locTo, locationType, location)
    }
  }
}

/**
 * Specifies which props to inject into your component.
 */
function collectDragSource (connect) {
  return {
    // Call this function inside render()
    // to let React DnD handle the drag events:
    connectDragSource: connect.dragSource()
  }
}

export class LocationsTabList extends React.Component {
  static propTypes = {
    location: PropTypes.object.isRequired,
    dropTargetType: PropTypes.string.isRequired,
    moveLocation: PropTypes.func.isRequired,
    connectDragSource: PropTypes.func.isRequired
  };

  render () {
    const { connectDragSource } = this.props
    const { location } = this.props

    return connectDragSource(
      <li className="list-group-item cursor-move p-2">
        <span className="drag-handle" />
        {location.name}
      </li>
    )
  }
}

export default DragSource(Types.LOC, locationSource, collectDragSource)(LocationsTabList)
