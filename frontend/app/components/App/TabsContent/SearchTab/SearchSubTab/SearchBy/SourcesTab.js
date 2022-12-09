import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import SourcesTabAvailSources from './SourcesTabAvailSources';
import SourcesTabSelectedSources from './SourcesTabSelectedSources';
import { Col } from 'reactstrap';

export class SourcesTab extends React.Component {
  static propTypes = {
    chosenMediaTypes: PropTypes.array.isRequired,
    chosenLanguages: PropTypes.array.isRequired,
    searchBySources: PropTypes.array.isRequired,
    selectedSearchBySources: PropTypes.array.isRequired,
    searchBySourcesType: PropTypes.string.isRequired,
    searchBySourcesQuery: PropTypes.string.isRequired,
    setSearchBySourcesQuery: PropTypes.func.isRequired,
    getSearchBySources: PropTypes.func.isRequired,
    addSelectedSearchBySource: PropTypes.func.isRequired,
    removeSelectedSearchBySource: PropTypes.func.isRequired,
    clearSearchBySources: PropTypes.func.isRequired,
    includeExcludeSearchBySources: PropTypes.func.isRequired
  };

  render() {
    const {
      searchBySourcesQuery,
      setSearchBySourcesQuery,
      chosenMediaTypes,
      chosenLanguages,
      searchBySources,
      getSearchBySources,
      addSelectedSearchBySource,
      searchBySourcesType,
      clearSearchBySources,
      selectedSearchBySources,
      removeSelectedSearchBySource,
      includeExcludeSearchBySources
    } = this.props;

    return (
      <Fragment>
        <Col sm={8}>
          <SourcesTabAvailSources
            searchBySourcesQuery={searchBySourcesQuery}
            selectedSources={selectedSearchBySources}
            setSearchBySourcesQuery={setSearchBySourcesQuery}
            chosenMediaTypes={chosenMediaTypes}
            chosenLanguages={chosenLanguages}
            availSources={searchBySources}
            getSearchBySources={getSearchBySources}
            addSelectedSearchBySource={addSelectedSearchBySource}
          />
        </Col>
        <Col sm={4}>
          <SourcesTabSelectedSources
            searchBySourcesType={searchBySourcesType}
            clearSearchBySources={clearSearchBySources}
            selectedSources={selectedSearchBySources}
            removeSelectedSearchBySource={removeSelectedSearchBySource}
            includeExcludeSearchBySources={includeExcludeSearchBySources}
          />
        </Col>
      </Fragment>
    );
  }
}

export default SourcesTab;
