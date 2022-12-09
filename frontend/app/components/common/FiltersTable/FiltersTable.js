import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import FilterGroup from './FilterGroup';
import { Button } from 'reactstrap';
import { arraymove } from '../../../common/helper';

export class FiltersTable extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    filters: PropTypes.object.isRequired,
    selectedFilters: PropTypes.object.isRequired,
    clearPending: PropTypes.object.isRequired,
    pages: PropTypes.object.isRequired,
    callbacks: PropTypes.object.isRequired
  };

  forEachGroup = (fn) => {
    const { pages, filters, selectedFilters } = this.props;
    const filterKeys = Object.keys(filters);

    arraymove(
      filterKeys,
      filterKeys.indexOf('sentiment'),
      filterKeys.indexOf('articleDate') + 1
    );

    return filterKeys.map((groupName) => {
      return fn(
        groupName,
        filters[groupName],
        pages[groupName],
        selectedFilters[groupName] || {}
      );
    });
  };

  onRefineButton = () => {
    this.props.callbacks.refine();
  };

  onClearAllButton = () => {
    this.props.callbacks.clearAllFilters();
    setTimeout(() => {
      this.props.callbacks.refine();
    }, 200);
  };

  render() {
    const { callbacks, clearPending, t } = this.props;
    return (
      <div className="filters-table-container">
        <div className="filters-table">
          {this.forEachGroup(
            (groupName, groupFilters, groupPage, selectedFilters) => {
              return (
                <FilterGroup
                  groupName={groupName}
                  key={groupName}
                  filters={groupFilters}
                  count={groupPage && groupPage.count}
                  totalCount={groupPage && groupPage.totalCount}
                  selectedFilters={selectedFilters}
                  clearPending={groupName in clearPending}
                  callbacks={callbacks}
                />
              );
            }
          )}
        </div>

        <div className="text-center mt-2">
          <Button
            onClick={this.onRefineButton}
            className="btn-icon mb-1 mr-1"
            color="warning"
          >
            <i className="lnr lnr-sync btn-icon-wrapper"></i>
            {t('filtersTable.refine')}
          </Button>
          <Button
            onClick={this.onClearAllButton}
            className="btn-icon mb-1"
            color="secondary"
          >
            <i className="lnr lnr-trash btn-icon-wrapper"></i>
            {t('filtersTable.clearAll')}
          </Button>
        </div>
      </div>
    );
  }
}

export default translate(['common'], { wait: true })(FiltersTable);
