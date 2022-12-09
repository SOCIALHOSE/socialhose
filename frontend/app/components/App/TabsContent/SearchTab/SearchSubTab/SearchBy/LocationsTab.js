/* eslint-disable react/jsx-no-bind */
import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import LocationsTabList from './LocationsTabList';
import { Button, Col, Row } from 'reactstrap';

export class LocationsTab extends React.Component {
  static propTypes = {
    locations: PropTypes.array.isRequired,
    locationsToInclude: PropTypes.array.isRequired,
    locationsToExclude: PropTypes.array.isRequired,
    chosenLocationsType: PropTypes.string.isRequired,
    changeLocationsType: PropTypes.func.isRequired,
    moveLocation: PropTypes.func.isRequired,
    clearLocations: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);

    this.state = {
      dropdownOpen: false,
      dropDownValue: 'country'
    };
  }

  onClearLocations = () => {
    this.props.clearLocations();
    this.props.changeLocationsType('country');
    this.setState({ dropDownValue: 'country' });
  };

  selectLocation = (value) => {
    this.props.changeLocationsType(value);
    this.setState({ dropDownValue: value });
  };

  render() {
    const {
      locations,
      chosenLocationsType,
      locationsToInclude,
      locationsToExclude
    } = this.props;
    const { t } = this.props;
    const locationsMainList = locations.filter((loc) => {
      return loc.type === chosenLocationsType;
    });
    const includeList = locationsToInclude.filter((loc) => {
      return loc.type === chosenLocationsType;
    });
    const excludeList = locationsToExclude.filter((loc) => {
      return loc.type === chosenLocationsType;
    });

    const { dropDownValue } = this.state;
    return (
      <Col sm={12}>
        <Button
          outline
          active={dropDownValue === 'country'}
          color="secondary"
          className="mr-2 mb-3"
          onClick={() => this.selectLocation('country')}
        >
          {t('searchTab.searchBySection.locations.countriesSelect')}
        </Button>
        <Button
          outline
          active={dropDownValue === 'state'}
          color="secondary"
          className="mb-3"
          onClick={() => this.selectLocation('state')}
        >
          {t('searchTab.searchBySection.locations.statesSelect')}
        </Button>

        <Row className="draggable">
          <Col md={4}>
            <LocationsTabList
              locations={locationsMainList}
              chosenLocationsType={chosenLocationsType}
              dropTargetType="locations"
              moveLocation={this.props.moveLocation}
            />
          </Col>
          <Col md={4}>
            <LocationsTabList
              locations={includeList}
              chosenLocationsType={chosenLocationsType}
              dropTargetType="locationsToInclude"
              moveLocation={this.props.moveLocation}
            />
          </Col>
          <Col md={4}>
            <LocationsTabList
              locations={excludeList}
              chosenLocationsType={chosenLocationsType}
              dropTargetType="locationsToExclude"
              moveLocation={this.props.moveLocation}
            />
          </Col>
        </Row>
      </Col>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(LocationsTab);
