import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Col, Row } from 'reactstrap';
import ECharts from '../../../../../common/charts/ECharts';
import ChartWrapper from '../ChartWrapper';
import {
  getBarOptions,
  getPieOptions
} from '../../../../../common/charts/ChartsOptions';
import { IoIosAdd, IoIosRefresh, IoIosCheckmark } from 'react-icons/io';
import reduxConnect from '../../../../../../redux/utils/connect';
import translate from 'react-i18next/dist/commonjs/translate';
import { compose } from 'redux';
import { getOverviewPieAPI } from '../../../../../../api/analytics/createAnalytics';
import useIsMounted from '../../../../../common/hooks/useIsMounted';

const initialBar = {
  data: [],
  error: undefined,
  loading: true,
  vertical: false
};
const initialPie = { data: [], error: undefined, loading: true };

function Demographics(props) {
  const { actions, analyze, feedData, id, t } = props;
  const isMounted = useIsMounted();
  const [barCountriesData, setBarCountriesData] = useState(initialBar);
  const [barLanguagesData, setBarLanguagesData] = useState(initialBar);
  const [genderData, setGenderData] = useState(initialPie);

  useEffect(() => {
    if (!id) {
      return;
    }
    // getCountriesData()
    getLanguagesData();
    getGenderData();
  }, []);

  useEffect(() => {
    if (barCountriesData.data) {
      setBarCountriesData((prev) => ({
        ...prev,
        data: {
          ...prev.data,
          xAxis: prev.data.yAxis,
          yAxis: prev.data.xAxis
        }
      }));
    }
  }, [barCountriesData.vertical]);

  useEffect(() => {
    if (barLanguagesData.data) {
      setBarLanguagesData((prev) => ({
        ...prev,
        data: {
          ...prev.data,
          xAxis: prev.data.yAxis,
          yAxis: prev.data.xAxis
        }
      }));
    }
  }, [barLanguagesData.vertical]);

  function updateResult(foo, id) {
    switch (id) {
      case cn.first:
        // getCountriesData()
        return;
      case cn.second:
        getLanguagesData();
        return;
      case cn.third:
        getGenderData();
        return;
      default:
        return;
    }
  }

  /* Uncomment when country chart shows up
  function getCountriesData() {
    setBarCountriesData((prev) => ({ ...prev, loading: true }))
    getOverviewPieAPI('country', id).then((res) => {
      if (!isMounted.current) {
        return false
      }
      if (res.error || !res.data.data) {
        // on error
        setBarCountriesData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }))
        return
      }
      const { data } = res.data
      const barOptions = {}
      const errors = {}
      Object.entries(data).forEach((feed) => {
        const [name, value] = feed
        const labels = ['Results']
        const datasets = Object.keys(value).map((item) => ({
          name: item,
          type: 'bar',
          data: [value[item]]
        }))

        if (!datasets || (Array.isArray(datasets) && datasets.length < 1)) {
          errors[name] = t('analyzeTab.noData');
        }

        barOptions[name] = getBarOptions(datasets, labels)
      })

      setBarCountriesData({
        data: barOptions,
        error: errors,
        loading: false,
        vertical: false
      })
    })
  } */

  function getLanguagesData() {
    setBarLanguagesData((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI('language', id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // on error
        setBarLanguagesData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }
      const { data } = res.data;
      const barOptions = {};
      const errors = {};
      Object.entries(data).forEach((feed) => {
        const [name, value] = feed;
        const labels = ['Results'];
        const datasets = Object.keys(value).map((item) => ({
          name: item,
          type: 'bar',
          data: [value[item]]
        }));

        if (!datasets || (Array.isArray(datasets) && datasets.length < 1)) {
          errors[name] = t('analyzeTab.noData');
        }

        barOptions[name] = getBarOptions(datasets, labels);
      });

      setBarLanguagesData({
        data: barOptions,
        error: errors,
        loading: false,
        vertical: false
      });
    });
  }

  function getGenderData() {
    setGenderData((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI('gender', id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // on error
        setGenderData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      const { data } = res.data;
      const pieOptions = {};
      const errors = {};

      Object.entries(data).forEach((feed) => {
        const [name, value] = feed;

        if (!value || (Array.isArray(value) && value.length < 1)) {
          errors[name] = t('analyzeTab.noData');
        }

        pieOptions[name] = getPieOptions(
          Object.entries(value).map((v) => ({
            name: v[0],
            value: v[1]
          }))
        );
      });

      setGenderData({
        data: pieOptions,
        error: errors,
        loading: false
      });
    });
  }

  function changeVertical(name, id) {
    name === cn.first
      ? setBarCountriesData((prev) => ({ ...prev, vertical: !prev.vertical }))
      : setBarLanguagesData((prev) => ({ ...prev, vertical: !prev.vertical }));
  }

  const hideChartAlert = (name, id) =>
    analyze.alertCharts.find((v) => v.name === name && v.id === id);
  const hideChartPieAlert = (id) =>
    analyze.alertCharts.find((v) => v.name === cn.third && v.id === id);

  const barchartMenus = (name, id) => [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: name, id }),
      showInMore: false,
      hide: hideChartAlert(name, id)
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChartAlert(name, id)
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, name),
      showInMore: false
    },
    /*  {
      title: t('analyzeTab.chartMenus.addToDashboard'),
      fn: () => {},
      showInMore: true
    }, */
    {
      title: t('analyzeTab.chartMenus.toggleHV'),
      fn: () => changeVertical(name, id),
      showInMore: true
    }
  ];

  const piechartMenus = (id) => [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.third, id }),
      showInMore: false,
      hide: hideChartPieAlert(id)
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChartPieAlert(id)
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.third),
      showInMore: false
    }
    // { title: t('analyzeTab.chartMenus.addToDashboard'), fn: () => {}, showInMore: true }
  ];

  return (
    <Row>
      {/* {feedData.feeds.map((feed) => (
        <Col key={feed.id} md="6">
          <ChartWrapper
            title={`${t('analyzeTab.charts.topLanguages')} (${feed.feed})`}
            menus={barchartMenus(cn.first, feed.id)}
          >
            <ECharts
              xLabel={barCountriesData.labels}
              loading={barCountriesData.loading}
              options={barCountriesData.data[feed.feed]}
              message={
                barCountriesData.error && barCountriesData.error[feed.feed]
              }
            />
          </ChartWrapper>
        </Col>
      ))} */}
      {feedData.feeds.map((feed) => (
        <Col key={feed.id} md="6">
          <ChartWrapper
            title={`${t('analyzeTab.charts.topLanguages')} (${feed.feed})`}
            menus={barchartMenus(cn.second, feed.id)}
          >
            <ECharts
              xLabel={barLanguagesData.labels}
              loading={barLanguagesData.loading}
              options={barLanguagesData.data[feed.feed]}
              message={
                barLanguagesData.error && barLanguagesData.error[feed.feed]
              }
            />
          </ChartWrapper>
        </Col>
      ))}
      {feedData.feeds.map((feed) => (
        <Col key={feed.id} md="6">
          <ChartWrapper
            title={`${t('analyzeTab.charts.gender')} (${feed.feed})`}
            menus={piechartMenus(feed.id)}
          >
            <ECharts
              loading={genderData.loading}
              options={genderData.data[feed.feed]}
              message={genderData.error && genderData.error[feed.feed]}
            />
          </ChartWrapper>
        </Col>
      ))}
    </Row>
  );
}

const cn = {
  first: 'Top Countries',
  second: 'Top Languages',
  third: 'Gender'
};

Demographics.propTypes = {
  t: PropTypes.func.isRequired,
  chartData: PropTypes.object,
  actions: PropTypes.object,
  id: PropTypes.string,
  feedData: PropTypes.object,
  analyze: PropTypes.object
};

const applyDecorators = compose(
  reduxConnect('analyze', ['appState', 'analyze']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(React.memo(Demographics));
