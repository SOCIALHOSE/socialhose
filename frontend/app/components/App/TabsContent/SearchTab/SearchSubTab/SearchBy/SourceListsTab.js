import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import SourceListsTabList from './SourceListsTabList';
import { Col } from 'reactstrap';

export class SourceListsTab extends React.Component {
  static propTypes = {
    searchBySourceLists: PropTypes.array.isRequired,
    searchBySourceListsToInclude: PropTypes.array.isRequired,
    searchBySourceListsToExclude: PropTypes.array.isRequired,
    getSourceLists: PropTypes.func.isRequired,
    moveSourceList: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  componentWillMount = () => {
    this.props.getSourceLists({ page: 1, limit: 25 });
  };

  render() {
    const {
      searchBySourceLists,
      searchBySourceListsToInclude,
      searchBySourceListsToExclude
    } = this.props;

    return (
      <Fragment>
        <Col md={4}>
          <SourceListsTabList
            sourceLists={searchBySourceLists}
            dropTargetType="searchBySourceListsAvailable"
            moveSourceList={this.props.moveSourceList}
          />
        </Col>
        <Col md={4}>
          <SourceListsTabList
            sourceLists={searchBySourceListsToInclude}
            dropTargetType="searchBySourceListsToInclude"
            moveSourceList={this.props.moveSourceList}
          />
        </Col>
        <Col md={4}>
          <SourceListsTabList
            sourceLists={searchBySourceListsToExclude}
            dropTargetType="searchBySourceListsToExclude"
            moveSourceList={this.props.moveSourceList}
          />
        </Col>
      </Fragment>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(SourceListsTab);
