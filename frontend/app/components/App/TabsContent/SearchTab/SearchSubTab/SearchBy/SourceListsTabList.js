import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { DropTarget } from 'react-dnd';
import flow from 'lodash/flow';
import SourceListsTabItem from './SourceListsTabItem';
import { ListGroup } from 'reactstrap';

const targetTypes = ['sourceList'];
const sourceListTarget = {
  drop(props, monitor, component) {
    if (monitor.didDrop()) {
      //check whether some nested
      // target already handled drop
      return;
    }

    return { newDropTargetType: props.dropTargetType };
  },

  canDrop(props, monitor) {
    return props.dropTargetType !== monitor.getItem().oldDropTargetType;
  }
};

function collectDropTarget(connect, monitor) {
  return {
    // Call this function inside render()
    // to let React DnD handle the drag events:
    connectDropTarget: connect.dropTarget(),
    // You can ask the monitor about the current drag state:
    itemType: monitor.getItemType()
  };
}

export class SourceListsTabList extends React.Component {
  static propTypes = {
    sourceLists: PropTypes.array.isRequired,
    dropTargetType: PropTypes.string.isRequired,
    moveSourceList: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired,
    connectDropTarget: PropTypes.func.isRequired
  };

  render() {
    const { sourceLists, dropTargetType } = this.props;
    const { t } = this.props;
    const { connectDropTarget } = this.props;

    return connectDropTarget(
      <div className="draggable scroll-area-md border b-radius-5">
        <p className="text-muted border-bottom p-2">
          {t('searchTab.searchBySection.sourceLists.' + dropTargetType)}
        </p>
        <ListGroup className="p-2">
          {sourceLists.map((sourceList, i) => {
            return (
              <SourceListsTabItem
                key={'sourceList-' + i}
                sourceList={sourceList}
                dropTargetType={dropTargetType}
                moveSourceList={this.props.moveSourceList}
              />
            );
          })}
        </ListGroup>
      </div>
    );
  }
}

export default flow(
  DropTarget(targetTypes, sourceListTarget, collectDropTarget),
  translate(['tabsContent'], { wait: true })
)(SourceListsTabList);
