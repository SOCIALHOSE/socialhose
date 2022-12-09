/* eslint-disable react/prop-types */
import React, {
  useState,
  useCallback,
  useMemo,
  Fragment,
  useEffect
} from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { compose } from 'redux';
import { Table } from '../../../../common/Table/Table';
import { savedAnalytics } from '../../../../../api/analytics/savedAnalytics';
import reduxConnect from '../../../../../redux/utils/connect';
import {
  getDate,
  getQueryParams,
  setDocumentData
} from '../../../../../common/helper';
import { Button } from 'reactstrap';
import DeleteDialog from './DeleteDialog';
import i18n from '../../../../../i18n';

function SavedAnalysisSubTab(props) {
  const [dataSource, setDataSource] = useState({ data: [] });
  const [loading, setLoading] = useState(true);
  const [deleteValues, setDeleteValues] = useState(false);
  const { t, actions } = props;

  useEffect(() => {
    setDocumentData('title', 'Saved Analysis | Analyze');
    return () => {
      setDocumentData('title');
    };
  }, []);

  const columns = useMemo(() => {
    const columnsList = [
      {
        id: 'feeds',
        Header: t('analyzeTab.savedAnalytics.feeds'),
        accessor: (d) => d.context.feeds,
        Cell: (props) =>
          props.value ? props.value.map((v) => v.name).join(', ') : ''
      },
      {
        id: 'date',
        Header: t('analyzeTab.savedAnalytics.dateRange'),
        accessor: (d) => d.context.rawFilters.date,
        Cell: (props) =>
          props.value
            ? `${getDate(props.value.start, 'MM/DD/YYYY')} to ${getDate(
                props.value.end,
                'MM/DD/YYYY'
              )}`
            : '-'
      },
      {
        Header: t('analyzeTab.savedAnalytics.createdAt'),
        accessor: 'createdAt',
        Cell: (props) => getDate(props.value, 'MM/DD/YYYY')
      },
      {
        Header: t('analyzeTab.savedAnalytics.actions'),
        accessor: 'id',
        Cell: (props) => getActions(props)
      }
    ];

    return columnsList;
  }, [getActions, i18n.language]);

  const getActions = useCallback((props) => {
    return (
      <div>
        <Button
          outline
          className="border-0 btn-transition"
          color="primary"
          size="sm"
          tag={Link}
          to={`/app/analyze/${props.value}/overview`}
        >
          {t('analyzeTab.savedAnalytics.view')}
        </Button>
        <Button
          outline
          className="border-0 btn-transition"
          color="secondary"
          tag={Link}
          to={`/app/analyze/edit/${props.value}`}
        >
          {t('analyzeTab.savedAnalytics.edit')}
        </Button>
        <Button
          outline
          className="border-0 btn-transition"
          color="secondary"
          onClick={function () {
            setDeleteValues(props);
          }}
        >
          {t('analyzeTab.savedAnalytics.delete')}
        </Button>
      </div>
    );
  }, []);

  const getSavedList = useCallback(
    (page, pageSize) => {
      setLoading(true);
      const params = getQueryParams({ page, pageSize });
      savedAnalytics(params).then((res) => {
        if (res.error || res.data === null || !res.data) {
          setLoading(false);
          return actions.addAlert({
            type: 'error',
            transKey: 'somethingWrong'
          });
        }
        res.data.length > 0 && setDataSource(res.data[0]);
        setLoading(false);
      });
    },
    [savedAnalytics]
  );

  const { data = [], totalCount = 0, limit = 10, page = 1 } = dataSource;
  return (
    <Fragment>
      <Table
        t={t}
        cardTitle={t('analyzeTab.savedAnalysis')}
        columns={columns}
        data={data}
        totalCount={totalCount}
        showTotalCount
        limit={limit}
        page={page}
        isLoading={loading}
        onFetchData={getSavedList}
      />
      {deleteValues && (
        <DeleteDialog
          data={deleteValues}
          actions={actions}
          toggle={function () {
            setDeleteValues(false);
          }}
          fetchData={function () {
            getSavedList(dataSource.page - 1, dataSource.limit);
          }}
        />
      )}
    </Fragment>
  );
}

SavedAnalysisSubTab.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object
};

const applyDecorators = compose(
  translate(['tabsContent'], { wait: true }),
  reduxConnect()
);

export default applyDecorators(SavedAnalysisSubTab);
