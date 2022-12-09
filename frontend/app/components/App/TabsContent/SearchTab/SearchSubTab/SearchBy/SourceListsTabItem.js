import React from 'react';
import PropTypes from 'prop-types';
import { DragSource } from 'react-dnd';

const Types = {
  SOURCE_LIST: 'sourceList'
};

const sourceListSource = {
  beginDrag(props) {
    // Return the data describing the dragged item
    return { oldDropTargetType: props.dropTargetType };
  },

  endDrag(props, monitor, component) {
    // When dropped on a compatible target, do something
    if (monitor.getDropResult() !== null) {
      const from = props.dropTargetType;
      const to = monitor.getDropResult().newDropTargetType;

      const sourceList = props.sourceList;

      props.moveSourceList(from, to, sourceList);
    }
  }
};

/**
 * Specifies which props to inject into your component.
 */
function collectDragSource(connect) {
  return {
    // Call this function inside render()
    // to let React DnD handle the drag events:
    connectDragSource: connect.dragSource()
  };
}

export class SourceListsTabItem extends React.Component {
  static propTypes = {
    sourceList: PropTypes.func.isRequired,
    dropTargetType: PropTypes.string.isRequired,
    moveSourceList: PropTypes.func.isRequired,
    connectDragSource: PropTypes.func.isRequired
  };

  render() {
    const { connectDragSource } = this.props;
    const { sourceList } = this.props;

    return connectDragSource(
      <li className="list-group-item cursor-move p-2">
        <span className="drag-handle" />
        {sourceList.name}
      </li>
    );
  }
}

export default DragSource(
  Types.SOURCE_LIST,
  sourceListSource,
  collectDragSource
)(SourceListsTabItem);
