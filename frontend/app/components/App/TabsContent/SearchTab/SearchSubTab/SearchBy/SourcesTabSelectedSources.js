/* eslint-disable react/jsx-no-bind */
import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Button, CustomInput, Table } from 'reactstrap';
import { IoIosCloseCircleOutline } from 'react-icons/io';
import { capitalize } from 'lodash';
import { getTitle } from '../../../../../../common/helper';

export class SourcesTabSelectedSources extends React.Component {
  static propTypes = {
    searchBySourcesType: PropTypes.string.isRequired,
    selectedSources: PropTypes.array.isRequired,
    removeSelectedSearchBySource: PropTypes.func.isRequired,
    clearSearchBySources: PropTypes.func.isRequired,
    includeExcludeSearchBySources: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  removeSource = (sourceId) => {
    this.props.removeSelectedSearchBySource(sourceId);
  };

  includeExclide = (type) => {
    this.props.includeExcludeSearchBySources(type);
  };

  render() {
    const { selectedSources } = this.props;
    const { t } = this.props;

    return (
      <Fragment>
        <div className="d-flex flex-wrap my-3">
          <CustomInput
            type="radio"
            name="include-exclude-source"
            className="d-flex mr-2"
            checked={this.props.searchBySourcesType === 'include'}
            id="include-sources-radio"
            onChange={() => this.includeExclide('include')}
            label={t('searchTab.searchBySection.sources.includeText')}
          />

          <CustomInput
            type="radio"
            name="include-exclude-source"
            checked={this.props.searchBySourcesType === 'exclude'}
            className="d-flex mr-2"
            id="exclude-sources-radio"
            onChange={() => this.includeExclide('exclude')}
            label={t('searchTab.searchBySection.sources.excludeText')}
          />
        </div>

        <p className="text-muted">
          {t('searchTab.searchBySection.sources.selectedSources')}
        </p>

        <div className="source-table-wrap border">
          <Table striped className="mb-0">
            <thead>
              <tr>
                <th>{t('searchTab.searchBySection.sources.source')}</th>
                <th>{t('searchTab.searchBySection.sources.mediatype')}</th>
                <th style={{ width: '50px' }}></th>
              </tr>
            </thead>
            <tbody>
              {selectedSources.length > 0 ? (
                selectedSources.map((source, i) => {
                  return (
                    <tr key={i}>
                      {/* <td>
                        <SourceIcon type={source.type} />
                      </td> */}
                      <td>{getTitle(source.title)}</td>
                      <td>{capitalize(source.type) || '-'}</td>
                      <td>
                        <button
                          title="Remove"
                          type="button"
                          className="btn p-0"
                          onClick={() => this.removeSource(source.id)}
                        >
                          <IoIosCloseCircleOutline
                            size={22}
                            className="text-danger ml-2"
                          />
                        </button>
                      </td>
                    </tr>
                  );
                })
              ) : (
                <tr className="p-4 text-center text-black-50">
                  <td colSpan="3">
                    {t('common:messages.noRows')} <br />
                    {t('searchTab.searchBySection.sources.selectSource')}
                  </td>
                </tr>
              )}
            </tbody>
          </Table>
        </div>
        {selectedSources.length > 0 && (
          <Button
            size="sm"
            className="d-block ml-auto mt-2 mb-2"
            onClick={this.props.clearSearchBySources}
          >
            {t('searchTab.clearBtn')}
          </Button>
        )}
      </Fragment>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(
  SourcesTabSelectedSources
);
