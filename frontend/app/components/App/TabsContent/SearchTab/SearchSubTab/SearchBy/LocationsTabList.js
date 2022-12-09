import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { DropTarget } from 'react-dnd';
import flow from 'lodash/flow';
import LocationItem from './LocationItem';
import {
  ListGroup
} from 'reactstrap';

const targetTypes = ['location'];
const locationListTarget = {
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

export class LocationsTabList extends React.Component {
  static propTypes = {
    locations: PropTypes.array.isRequired,
    chosenLocationsType: PropTypes.string.isRequired,
    dropTargetType: PropTypes.string.isRequired,
    moveLocation: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired,
    connectDropTarget: PropTypes.func.isRequired
  };

  render() {
    const { locations, chosenLocationsType, dropTargetType } = this.props;
    const { t } = this.props;
    const { connectDropTarget } = this.props;

    locations.forEach((location) => {
      location.name = t('common:' + location.type + '.' + location.code);
    });

    const sortedLocations = locations.sort((a, b) => {
      const nameA = a.name.toLowerCase();
      const nameB = b.name.toLowerCase();
      if (nameA < nameB) {
        //sort string ascending
        return -1;
      }
      if (nameA > nameB) {
        return 1;
      }
      return 0;
    });

    return connectDropTarget(
      <div className="scroll-area-md border b-radius-5">
        <p className="text-muted border-bottom p-2">{t('searchTab.searchBySection.locations.' + dropTargetType)}</p>
        <ListGroup className="p-2">
          {sortedLocations.map((location, i) => {
            return (
              <LocationItem
                key={'location-' + i}
                location={location}
                dropTargetType={dropTargetType}
                locationType={chosenLocationsType}
                moveLocation={this.props.moveLocation}
              />
            );
          })}
        </ListGroup>
      </div>
    );
  }
}

export default flow(
  DropTarget(targetTypes, locationListTarget, collectDropTarget),
  translate(['tabsContent'], { wait: true })
)(LocationsTabList);
