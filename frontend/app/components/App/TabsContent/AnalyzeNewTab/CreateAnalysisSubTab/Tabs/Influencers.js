/* eslint-disable react/prop-types */
import React, {
  useState,
  useCallback,
  Fragment,
  useEffect,
  useMemo
} from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { compose } from 'redux';
import { Table } from '../../../../../common/Table/Table';
import { getInfluencersAPI } from '../../../../../../api/analytics/createAnalytics';
import { reduxActions } from '../../../../../../redux/utils/connect';
import {
  getQueryParams,
  removeHttpsUrl,
  capOnlyFirstLetter,
  getValidHttpUrl
} from '../../../../../../common/helper';
import i18n from '../../../../../../i18n';

function Influencers(props) {
  const [dataSource, setDataSource] = useState(null);
  const [loading, setLoading] = useState(true);
  const [filter] = useState(filtersNames[1].id);
  const { t, actions, id, feedData } = props;

  useEffect(() => {
    if (!id || !dataSource) {
      return;
    }
    getInfluencers(); //called from table
  }, [filter]);

  const getDetailsColumns = (id) => {
    return id === filtersNames[0].id ? sourceDetails : authorDetails;
  };

  const authorDetails = useMemo(
    () => [
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.rank'),
        accessor: 'source_hashcode',
        Cell: (row) => (
          <div style={{ textAlign: 'center' }}>{row.index + 1}</div>
        ),
        minWidth: 52
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.influencers'),
        accessor: 'influence',
        Cell: (row) =>
          getValidHttpUrl(row.value) ? (
            <a
              target="_blank"
              rel="nofollow noopener"
              href={getValidHttpUrl(row.value)}
            >
              {row.original && row.original.author_name}
            </a>
          ) : (
            removeHttpsUrl(row.value)
          ),
        minWidth: 130
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.sourceType'),
        accessor: 'source_type',
        Cell: (row) => capOnlyFirstLetter(row.value),
        minWidth: 102
      }
    ],
    [i18n.language]
  );

  const sourceDetails = useMemo(
    () => [
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.rank'),
        accessor: 'source_hashcode',
        Cell: (row) => (
          <div style={{ textAlign: 'center' }}>{row.index + 1}</div>
        ),
        minWidth: 52
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.influencers'),
        accessor: 'influence',
        Cell: (row) =>
          getValidHttpUrl(row.value) ? (
            <a
              target="_blank"
              rel="nofollow noopener"
              href={getValidHttpUrl(row.value)}
            >
              {removeHttpsUrl(row.value)}
            </a>
          ) : (
            removeHttpsUrl(row.value)
          ),
        minWidth: 130
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.sourceType'),
        accessor: 'source_type',
        Cell: (row) => capOnlyFirstLetter(row.value),
        minWidth: 102
      }
    ],
    [i18n.language]
  );

  const sentimentColumns = useMemo(
    () => [
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.total'),
        // accessor: d => d.nop.total
        accessor: 'totalSentiment',
        minWidth: 52,
        Cell: (row) => (
          <div style={{ textAlign: 'center' }}>{row.value || 0}</div>
        )
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.positive'),
        accessor: 'POSITIVE',
        minWidth: 78,
        Cell: (row) => (
          <div style={{ textAlign: 'center' }}>{row.value || 0}</div>
        )
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.neutral'),
        accessor: 'NEUTRAL',
        minWidth: 78,
        Cell: (row) => (
          <div style={{ textAlign: 'center' }}>{row.value || 0}</div>
        )
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.negative'),
        accessor: 'NEGATIVE',
        minWidth: 78,
        Cell: (row) => (
          <div style={{ textAlign: 'center' }}>{row.value || 0}</div>
        )
      }
    ],
    [i18n.language]
  );

  const reachColumns = useMemo(
    () => [
      /*   {
    Header: i18n.t('tabsContent:analyzeTab.influencerCols.reach'),
    accessor: 'reach',
    minWidth: 65,
    Cell: (row) => <div style={{ textAlign: 'center' }}>{row.value || 0}</div>
  }, */
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.engagement'),
        accessor: 'engagement',
        minWidth: 105,
        Cell: (row) => (
          <div style={{ textAlign: 'center' }}>{row.value || 0}</div>
        )
      }
      /*   {
    Header: i18n.t('tabsContent:analyzeTab.influencerCols.engagementPerMention'),
    accessor: 'engagement_per_mention',
    Cell: (row) => <div style={{ textAlign: 'center' }}>{row.value || 0}</div>
  } */
    ],
    [i18n.language]
  );

  const columnsList = useMemo(
    () => [
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.details'),
        headerClassName: 'text-center',
        columns: getDetailsColumns(filter)
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.sentiments'),
        headerClassName: 'text-center',
        columns: sentimentColumns
      },
      {
        Header: i18n.t('tabsContent:analyzeTab.influencerCols.reach'),
        headerClassName: 'text-center',
        columns: reachColumns
      }
    ],
    [filter, i18n.language]
  );

  const getInfluencers = useCallback(
    (page = 0, pageSize = 10) => {
      setLoading(true);
      const filterParams = getQueryParams({ page, pageSize });
      getInfluencersAPI(id, filter, filterParams).then((res) => {
        // if (false) {
        if (res.error || res.data === null || !res.data.data) {
          setLoading(false);
          return actions.addAlert({
            type: 'error',
            transKey: 'somethingWrong'
          });
        }

        const tableData = {};
        res.data.data.forEach((v) => {
          tableData[v.name] = v.data;
        });
        setDataSource(tableData);
        setLoading(false);
      });
    },
    [id, filter]
  );

  return (
    <Fragment>
      {/* <ButtonGroup size="sm" className="mb-3 d-block text-right">
        {filtersNames.map((item) => (
          <Button
            outline
            key={item.id}
            title={item.name}
            color="secondary"
            onClick={function () {
              setFilter(item.id)
            }}
            active={filter === item.id}
          >
            {item.name}
          </Button>
        ))}
      </ButtonGroup> */}
      {feedData.feeds.map((feed) => {
        let tableData = dataSource;
        if (!tableData || !tableData[feed.feed]) {
          tableData = { [feed.feed]: [] };
          // uncomment for pagination
          // tableData[feed.feed] = { data: [], totalCount: 0, limit: 0, page: 0 }
        }

        const { totalCount = 0, limit = 0, page = 0 } = tableData[feed.feed];
        return (
          <Table
            key={feed.id}
            t={t}
            cardTitle={`${t('analyzeTab.charts.topInfluencers')} (${
              feed.feed
            })`}
            columns={columnsList}
            data={tableData[feed.feed]}
            totalCount={totalCount}
            showTotalCount
            limit={limit}
            page={page}
            isLoading={loading}
            onFetchData={getInfluencers}
          />
        );
      })}
    </Fragment>
  );
}

const filtersNames = [
  { name: 'Source', id: 0 },
  { name: 'Author', id: 1 }
];

Influencers.propTypes = {
  t: PropTypes.func.isRequired,
  feedData: PropTypes.object,
  id: PropTypes.string,
  actions: PropTypes.object
};

const applyDecorators = compose(
  translate(['tabsContent'], { wait: true }),
  reduxActions()
);

export default applyDecorators(Influencers);
