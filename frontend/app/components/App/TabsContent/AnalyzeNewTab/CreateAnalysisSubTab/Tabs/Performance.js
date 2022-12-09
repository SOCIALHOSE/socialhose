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
import {
  getEngagementsAPI,
  getEngagementsTimeAPI,
  getOverviewBarAPI,
  getOverviewPieAPI
} from '../../../../../../api/analytics/createAnalytics';
import useIsMounted from '../../../../../common/hooks/useIsMounted';

function Performance(props) {
  const { actions, analyze, feedData, id, t } = props;
  const isMounted = useIsMounted();
  const [barData, setBarData] = useState({
    data: [],
    error: undefined,
    loading: true,
    vertical: false
  });
  const [engBarData, setEngBarData] = useState({
    data: [],
    error: undefined,
    loading: true,
    vertical: false
  });
  const [potentialBarData, setPotentialBarData] = useState({
    data: [],
    error: undefined,
    loading: true,
    vertical: false
  });
  const [sentimentBar, setSentimentBar] = useState({
    data: [],
    error: undefined,
    loading: true
  });
  const [pieMentions, setpieMentions] = useState({
    data: [],
    error: undefined,
    loading: true
  });
  const [pieEng, setpieEng] = useState({
    data: [],
    error: undefined,
    loading: true
  });
  /*  const [pieReach, setpieReach] = useState({
    data: [],
    error: undefined,
    loading: true
  }); */

  useEffect(() => {
    // pass filter
    if (!id) {
      return;
    }
    getBarChart();
    getEngBarChart();
    // getPotentialChart()
    getSentimentChart();
    getpieMentions();
    getpieEngg();
    // getpieReach()
  }, []);

  function updateResult(foo, id) {
    switch (id) {
      case cn.first:
        getBarChart();
        return;
      case cn.second:
        getEngBarChart();
        return;
      case cn.third:
        // getPotentialChart() // Uncomment when API has data
        return;
      case cn.fourth:
        getSentimentChart();
        return;
      case cn.fifth:
        getpieMentions();
        return;
      case cn.sixth:
        getpieEngg();
        return;
      case cn.seventh:
        // getpieReach() // Uncomment when API has data
        return;
      default:
        return;
    }
  }

  useEffect(() => {
    if (barData.data) {
      setBarData((prev) => ({
        ...prev,
        data: {
          ...prev.data,
          xAxis: prev.data.yAxis,
          yAxis: prev.data.xAxis
        }
      }));
    }
  }, [barData.vertical]);

  useEffect(() => {
    if (engBarData.data) {
      setEngBarData((prev) => ({
        ...prev,
        data: {
          ...prev.data,
          xAxis: prev.data.yAxis,
          yAxis: prev.data.xAxis
        }
      }));
    }
  }, [engBarData.vertical]);

  useEffect(() => {
    if (potentialBarData.data) {
      setPotentialBarData((prev) => ({
        ...prev,
        data: {
          ...prev.data,
          xAxis: prev.data.yAxis,
          yAxis: prev.data.xAxis
        }
      }));
    }
  }, [potentialBarData.vertical]);

  function getBarChart() {
    setBarData((prev) => ({ ...prev, loading: true }));
    getOverviewBarAPI('none', id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // on error
        setBarData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }
      const { data } = res.data;
      const labels = Object.keys(data[0].data);

      const datasets = data.map((item) => ({
        name: item.name,
        type: barData.vertical ? 'bar' : 'line',
        smooth: true,
        data: Object.values(item.data)
      }));

      const barOptions = getBarOptions(datasets, labels);

      setBarData({
        data: barOptions,
        error: false,
        loading: false,
        vertical: false
      });
    });
  }

  function getEngBarChart() {
    setEngBarData((prev) => ({ ...prev, loading: true }));
    getEngagementsTimeAPI(id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // on error
        setEngBarData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      const { data } = res.data;
      const labels = Object.keys(data[0].data);
      const datasets = data.map((item) => ({
        name: item.name,
        type: barData.vertical ? 'bar' : 'line',
        smooth: true,
        data: Object.values(item.data)
      }));

      const barOptions = getBarOptions(datasets, labels);

      setEngBarData({
        data: barOptions,
        error: false,
        loading: false,
        vertical: false
      });
    });
  }
  /* 
  function getPotentialChart() {
    setPotentialBarData((prev) => ({ ...prev, loading: true }));
    getOverviewBarAPI('none', id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // on error
        setPotentialBarData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }
      const { data } = res.data;
      const labels = Object.keys(data);

      const datasets = {
        name: 'Potential reach over time',
        type: potentialBarData.vertical ? 'bar' : 'line',
        smooth: true,
        data: Object.values(data)
      };

      const barOptions = getBarOptions(datasets, labels);

      setPotentialBarData({
        data: barOptions,
        error: false,
        loading: false,
        vertical: false
      });
    });
  } */

  function getSentimentChart() {
    setSentimentBar((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI('sentiment', id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // on error
        setSentimentBar((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }
      const { data } = res.data;
      const barOptions = {};
      Object.keys(data).forEach((feed) => {
        const labels = ['Results'];
        const datasets = ['POSITIVE', 'NEGATIVE', 'NEUTRAL'].map((item) => ({
          name: item,
          type: 'bar',
          data: [data[feed][item]]
        }));

        barOptions[feed] = getBarOptions(datasets, labels);
      });

      setSentimentBar({
        data: barOptions,
        error: false,
        loading: false,
        vertical: false
      });
    });
  }

  function getpieMentions() {
    setpieMentions((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI('none', id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // alert on error
        setpieMentions((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      const { data } = res.data;
      const pieOptions = getPieOptions(
        Object.entries(data).map((v) => ({ name: v[0], value: v[1] }))
      );

      setpieMentions({
        data: pieOptions,
        error: false,
        loading: false
      });
    });
  }

  function getpieEngg() {
    setpieEng((prev) => ({ ...prev, loading: true }));
    getEngagementsAPI(id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // alert on error
        setpieEng((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      // condition for other filter than 0
      const { data } = res.data;
      const pieOptions = getPieOptions(
        Object.entries(data).map((v) => ({ name: v[0], value: v[1] }))
      );

      setpieEng({
        data: pieOptions,
        error: false,
        loading: false
      });
    });
  }
  /* 
  function getpieReach() {
    setpieReach((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI('none', id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // alert on error
        setpieReach((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      const { data } = res.data;
      const pieOptions = getPieOptions(
        Object.entries(data).map((v) => ({ name: v[0], value: v[1] }))
      );

      setpieReach({
        data: pieOptions,
        error: false,
        loading: false
      });
    });
  } */

  function changeVertical(chart) {
    switch (chart) {
      case cn.first:
        setBarData((prev) => ({ ...prev, vertical: !prev.vertical }));
        return;
      case cn.second:
        setEngBarData((prev) => ({ ...prev, vertical: !prev.vertical }));
        return;
      case cn.third:
        setPotentialBarData((prev) => ({ ...prev, vertical: !prev.vertical }));
        return;
      default:
        return;
    }
  }

  const hideChart1Alert = analyze.alertCharts.find((v) => v.name === cn.first);
  const hideChart2Alert = analyze.alertCharts.find((v) => v.name === cn.second);
  // const hideChart3Alert = analyze.alertCharts.find((v) => v.name === cn.third);
  const hideChart4Alert = (id) =>
    analyze.alertCharts.find((v) => v.name === cn.fourth && v.id === id);
  const hideChart5Alert = analyze.alertCharts.find((v) => v.name === cn.fifth);
  const hideChart6Alert = analyze.alertCharts.find((v) => v.name === cn.sixth);
  /*   const hideChart7Alert = analyze.alertCharts.find(
    (v) => v.name === cn.seventh
  ); */

  const barchart1Menus = [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.first, id: 'none' }),
      showInMore: false,
      hide: hideChart1Alert
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart1Alert
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.first),
      showInMore: false
    },
    /*    {
      title: t('analyzeTab.chartMenus.addToDashboard'),
      fn: () => {},
      showInMore: true
    }, */
    {
      title: t('analyzeTab.chartMenus.toggleHV'),
      fn: () => changeVertical(cn.first),
      showInMore: true
    }
  ];

  const barchart2Menus = [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.second, id: 'none' }),
      showInMore: false,
      hide: hideChart2Alert
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart2Alert
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.second),
      showInMore: false
    },
    /* {
      title: t('analyzeTab.chartMenus.addToDashboard'),
      fn: () => {},
      showInMore: true
    }, */
    {
      title: t('analyzeTab.chartMenus.toggleHV'),
      fn: () => changeVertical(cn.second),
      showInMore: true
    }
  ];
  /* 
  const barchart3Menus = [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.third, id: 'none' }),
      showInMore: false,
      hide: hideChart3Alert
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart3Alert
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.third),
      showInMore: false
    },
    {
      title: t('analyzeTab.chartMenus.addToDashboard'),
      fn: () => {},
      showInMore: true
    },
    {
      title: t('analyzeTab.chartMenus.toggleHV'),
      fn: () => changeVertical(cn.third),
      showInMore: true
    }
  ];
 */
  function barchart4Menus(id) {
    return [
      {
        title: '', // t('analyzeTab.chartMenus.addToAlert'),
        icon: IoIosAdd,
        size: 24,
        fn: () => actions.addAlertChart({ name: cn.fourth, id }),
        showInMore: false,
        hide: hideChart4Alert(id)
      },
      {
        title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
        icon: IoIosCheckmark,
        size: 24,
        showInMore: false,
        hide: !hideChart4Alert(id)
      },
      {
        title: t('analyzeTab.chartMenus.refresh'),
        icon: IoIosRefresh,
        fn: () => updateResult(null, cn.fourth, id),
        showInMore: false
      }
      /* {
         title: t('analyzeTab.chartMenus.addToDashboard'),
        fn: () => {},
        showInMore: true
      } */
    ];
  }

  const pieChart1 = [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.fifth, id: 'none' }),
      showInMore: false,
      hide: hideChart5Alert
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart5Alert
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.fifth),
      showInMore: false
    }
    // {  title: t('analyzeTab.chartMenus.addToDashboard'), fn: () => {}, showInMore: true }
  ];

  const pieChart2 = [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.sixth, id: 'none' }),
      showInMore: false,
      hide: hideChart6Alert
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart6Alert
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.sixth),
      showInMore: false
    }
    // {  title: t('analyzeTab.chartMenus.addToDashboard'), fn: () => {}, showInMore: true }
  ];
  /* 
  const pieChart3 = [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.seventh, id: 'none' }),
      showInMore: false,
      hide: hideChart7Alert
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart7Alert
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.seventh),
      showInMore: false
    }
    // {  title: t('analyzeTab.chartMenus.addToDashboard'), fn: () => {}, showInMore: true }
  ];
 */
  return (
    <Row>
      <Col md="8">
        <ChartWrapper
          title={t('analyzeTab.charts.mentionsOverTime')}
          menus={barchart1Menus}
        >
          <ECharts
            xLabel={barData.labels}
            loading={barData.loading}
            options={barData.data}
          />
        </ChartWrapper>
      </Col>
      <Col md="4">
        <ChartWrapper title={t('analyzeTab.charts.mentions')} menus={pieChart1}>
          <ECharts
            xLabel={pieMentions.labels}
            loading={pieMentions.loading}
            options={pieMentions.data}
          />
        </ChartWrapper>
      </Col>
      <Col md="8">
        <ChartWrapper
          title={t('analyzeTab.charts.engagementOverTime')}
          menus={barchart2Menus}
        >
          <ECharts
            xLabel={engBarData.labels}
            loading={engBarData.loading}
            options={engBarData.data}
          />
        </ChartWrapper>
      </Col>
      <Col md="4">
        <ChartWrapper
          title={t('analyzeTab.charts.engagement')}
          menus={pieChart2}
        >
          <ECharts
            xLabel={pieEng.labels}
            loading={pieEng.loading}
            options={pieEng.data}
          />
        </ChartWrapper>
      </Col>
      {/* <Col md="8">
        <ChartWrapper title={t('analyzeTab.charts.potentialReachOverTime')} menus={barchart3Menus}>
          <ECharts
            xLabel={potentialBarData.labels}
            loading={potentialBarData.loading}
            options={potentialBarData.data}
          />
        </ChartWrapper>
      </Col>
      <Col md="4">
        <ChartWrapper title={t('analyzeTab.charts.potentialReach')} menus={pieChart3}>
          <ECharts
            xLabel={pieReach.labels}
            loading={pieReach.loading}
            options={pieReach.data}
          />
        </ChartWrapper>
      </Col> */}
      {feedData.feeds.map((feed) => (
        <Col md="12" key={feed.id}>
          <ChartWrapper
            title={`${t('analyzeTab.charts.proportionofSentiment')} (${
              feed.feed
            })`}
            menus={barchart4Menus(feed.id)}
          >
            <ECharts
              xLabel={sentimentBar.labels}
              loading={sentimentBar.loading}
              options={sentimentBar.data[feed.feed]}
            />
          </ChartWrapper>
        </Col>
      ))}
    </Row>
  );
}

const cn = {
  first: 'Mentions over time',
  second: 'Engagement over time',
  third: 'Potential reach over time',
  fourth: 'Proportion of sentiment',
  fifth: 'Mentions',
  sixth: 'Engagement',
  seventh: 'Potential Reach'
};

Performance.propTypes = {
  chartData: PropTypes.object,
  actions: PropTypes.object,
  feedData: PropTypes.object,
  id: PropTypes.string,
  analyze: PropTypes.object,
  t: PropTypes.func
};

const applyDecorators = compose(
  reduxConnect('analyze', ['appState', 'analyze']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(React.memo(Performance));
