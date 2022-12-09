import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
// import SourceIcon from './SourceIcon';
import { Button, Input, InputGroup, InputGroupAddon, Table } from 'reactstrap';
import { capitalize } from 'lodash';
import { getTitle } from '../../../../../../common/helper';
import cx from 'classnames';
import { domainNames } from '../SearchSubTab';

export class SourcesTabAvailSources extends React.Component {
  static propTypes = {
    chosenMediaTypes: PropTypes.array.isRequired,
    chosenLanguages: PropTypes.array.isRequired,
    availSources: PropTypes.array.isRequired,
    selectedSources: PropTypes.array.isRequired,
    searchBySourcesQuery: PropTypes.string.isRequired,
    setSearchBySourcesQuery: PropTypes.func.isRequired,
    getSearchBySources: PropTypes.func.isRequired,
    addSelectedSearchBySource: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  componentDidMount = () => {
    this.searchSources();
  };

  searchSources = () => {
    const {
      chosenLanguages,
      chosenMediaTypes,
      getSearchBySources,
      searchBySourcesQuery
    } = this.props;
    const query = searchBySourcesQuery;
    const dataToSend = {};
    dataToSend.page = 1;
    dataToSend.limit = 100;
    dataToSend.query = query;
    dataToSend.filters = {};

    const source = []
    const domain = []
    chosenMediaTypes.map((v) => {
      if (domainNames.includes(v)) {
        domain.push(`${v}.com`);
      } else {
        source.push(v);
      }
    })
    dataToSend.filters.publisher = { source, domain };
    
    dataToSend.filters.language = chosenLanguages;
    getSearchBySources(dataToSend);
  };

  chooseSource = (e) => {
    const dataset = e.currentTarget.dataset;
    const sourceTitle = dataset.sourceTitle;
    const sourceType = dataset.sourceType;
    const sourceId = dataset.sourceId;
    this.props.addSelectedSearchBySource({
      title: sourceTitle,
      type: sourceType,
      id: sourceId
    });
  };

  onChangeSearchInput = (e) => {
    const val = e.target.value;
    this.props.setSearchBySourcesQuery(val);
  };

  onEnterSearchInput = (e) => {
    if (e.keyCode === 13) this.searchSources();
  };

  render() {
    const { availSources, selectedSources } = this.props;
    const { t } = this.props;

    return (
      <Fragment>
        <InputGroup className="mb-3">
          <Input
            type="text"
            id="search-by-sources-input"
            value={this.props.searchBySourcesQuery}
            onChange={this.onChangeSearchInput}
            onKeyUp={this.onEnterSearchInput}
          />
          <InputGroupAddon addonType="append">
            <Button
              color="primary"
              className="btn-icon btn-icon-only"
              onClick={this.searchSources}
            >
              <i className="lnr-magnifier btn-icon-wrapper"></i>
            </Button>
          </InputGroupAddon>
        </InputGroup>

        <p className="text-muted">
          {t('searchTab.searchBySection.sources.availSources')}
        </p>
        <div className="source-table-wrap border">
          <Table striped bordered className="mb-0">
            <thead>
              <tr>
                <th>{t('searchTab.searchBySection.sources.source')}</th>
                <th>{t('searchTab.searchBySection.sources.siteType')}</th>
                <th>{t('searchTab.searchBySection.sources.mediatype')}</th>
                <th>{t('searchTab.searchBySection.sources.lang')}</th>
              </tr>
            </thead>
            <tbody>
              {availSources.length > 0 ? (
                availSources.map((source, i) => {
                  return (
                    <tr
                      title="Click to select"
                      className={cx('clickable', {
                        active:
                          selectedSources &&
                          selectedSources.find((v) => v.id === source.id)
                      })}
                      data-source-title={source.title}
                      data-source-type={source.type}
                      data-source-id={source.id}
                      onClick={this.chooseSource}
                      key={i}
                    >
                      {/* <td>
                        <SourceIcon type={source.type} />
                      </td> */}
                      <td>{getTitle(source.title)}</td>
                      <td title={source.url}>
                        {capitalize(source.siteType) || '-'}
                      </td>
                      <td>{capitalize(source.type) || '-'}</td>
                      <td>{t(`common:language.${source.lang}`)}</td>
                    </tr>
                  );
                })
              ) : (
                <tr className="p-4 text-center text-black-50">
                  <td colSpan="4">{t('common:messages.noRows')}</td>
                </tr>
              )}
            </tbody>
          </Table>
        </div>
      </Fragment>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(
  SourcesTabAvailSources
);
