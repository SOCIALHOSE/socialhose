import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import FilterItem from './FilterItem';
import { ADV_FILTERS_LIMIT } from '../../../redux/modules/appState/search';
import classnames from 'classnames';
import { Button } from 'reactstrap';

export class FilterGroup extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    groupName: PropTypes.string.isRequired,
    filters: PropTypes.array.isRequired,
    selectedFilters: PropTypes.object,
    count: PropTypes.number.isRequired,
    totalCount: PropTypes.number.isRequired,
    clearPending: PropTypes.bool.isRequired,
    callbacks: PropTypes.object.isRequired
  };

  static translatableGroups = {
    articleDate: 'articleDate',
    country: 'country',
    language: 'language',
    articleLanguage: 'language',
    sourceCountry: 'country',
    sentiment: 'sentiment'
  };

  translateItem = (itemName) => {
    return this.props.t(`${this.translateKey}.${itemName}`, {
      defaultValue: itemName
    });
  };

  constructor(props) {
    super(props);
    this.state = { isExpanded: !!this.props.totalCount };
    if (this.props.groupName in FilterGroup.translatableGroups) {
      this.translateKey = FilterGroup.translatableGroups[this.props.groupName];
    } else {
      this.translateItem = (x) => x; //equiv function
    }
  }

  componentDidUpdate = (prevProps) => {
    const needUpdate = prevProps.totalCount !== this.props.totalCount;
    if (needUpdate) {
      this.setState({
        isExpanded: !!this.props.totalCount
      });
    }
  };

  toggleExpand = () => {
    this.setState({
      isExpanded: !this.state.isExpanded
    });
  };

  onMoreClick = () => {
    this.props.callbacks.moreFilters(this.props.groupName);
  };

  onLessClick = () => {
    this.props.callbacks.lessFilters(this.props.groupName);
  };

  onRefineClick = () => {
    this.props.callbacks.refine();
  };

  onClearClick = () => {
    this.props.callbacks.clearFilters(this.props.groupName);
  };

  onItemClick = (filterValue) => {
    this.props.callbacks.selectFilter(this.props.groupName, filterValue);
  };

  forEachItem = (fn) => {
    return this.props.filters && this.props.filters.slice(0, this.props.count).map(fn);
  };

  render() {
    const clsName = 'filters-table__group';
    const { t, selectedFilters, clearPending, count, totalCount } = this.props;

    let includedCounter = 0;
    let excludedCounter = 0;
    for (let value in selectedFilters) {
      if (selectedFilters.hasOwnProperty(value)) {
        if (selectedFilters[value] === -1) excludedCounter++;
        if (selectedFilters[value] === 1) includedCounter++;
      }
    }

    const moreVisible = count < totalCount;
    const lessVisible = count > ADV_FILTERS_LIMIT;
    const hasSelected = !!excludedCounter || !!includedCounter;

    const moreVisibleClass = classnames('filters-table__more', {
      'filters-table__more--visible': moreVisible,
      'filters-table__more--hidden': !moreVisible && lessVisible,
      'filters-table__more--none': !moreVisible && !lessVisible
    });

    const lessVisibleClass = classnames('filters-table__less', {
      'filters-table__less--visible': lessVisible,
      'filters-table__less--hidden': !lessVisible && moreVisible,
      'filters-table__less--none': !lessVisible && !moreVisible
    });

    const refineVisibleClass = classnames(
      'filters-table__more my-2 ml-2 mr-1',
      {
        'filters-table__more--none': !hasSelected && !clearPending
      }
    );

    const clearVisibleClass = classnames('filters-table__less my-2 mr-2 ml-1', {
      'filters-table__less--none': !hasSelected
    });

    const isRTL = document.documentElement.dir === 'rtl';

    return (
      <div
        className={
          clsName + (this.state.isExpanded ? ' ' + clsName + '--expanded' : '')
        }
      >
        <div className="filters-table__head" onClick={this.toggleExpand}>
          {isRTL ? (
            <i className="fa fa-chevron-left" />
          ) : (
            <i className="fa fa-chevron-right" />
          )}
          <i className="fa fa-chevron-down" />
          {t('advancedFilters.' + this.props.groupName, {
            defaultValue: this.props.groupName
          })}
          <div>
            {!includedCounter && !excludedCounter && (
              <i style={{ marginRight: '5px' }} className="lnr lnr-cross" />
            )}
            {!!includedCounter && (
              <span className="included"> {includedCounter}</span>
            )}
            {!!excludedCounter && (
              <span className="excluded">-{excludedCounter}</span>
            )}
          </div>
        </div>

        {this.state.isExpanded && (
          <div className="filters-table__body">
            {this.forEachItem((item) => {
              return (
                <FilterItem
                  value={item.value}
                  title={this.translateItem(item.value)}
                  key={item.value}
                  count={item.count}
                  selectionState={selectedFilters[item.value]}
                  onItemClick={this.onItemClick}
                />
              );
            })}

            {clearPending && (
              <div className="filters-table__clear-msg">
                {t('filtersTable.clearMessage')}
              </div>
            )}

            <div className="d-flex justify-content-between">
              <Button
                className={moreVisibleClass}
                color="link"
                onClick={this.onMoreClick}
              >
                <i className="fa fa-angle-double-down" />{' '}
                {t('filtersTable.more')}
              </Button>

              <Button
                className={lessVisibleClass}
                color="link"
                onClick={this.onLessClick}
              >
                <i className="fa fa-angle-double-up" /> {t('filtersTable.less')}
              </Button>
            </div>

            <div className="d-flex justify-content-between">
              <Button
                className={refineVisibleClass}
                color="primary"
                onClick={this.onRefineClick}
              >
                {t('filtersTable.refine')}
              </Button>

              <Button className={clearVisibleClass} onClick={this.onClearClick}>
                {t('filtersTable.clear')}
              </Button>
            </div>
          </div>
        )}
      </div>
    );
  }
}

export default translate(['common'], { wait: true })(FilterGroup);
