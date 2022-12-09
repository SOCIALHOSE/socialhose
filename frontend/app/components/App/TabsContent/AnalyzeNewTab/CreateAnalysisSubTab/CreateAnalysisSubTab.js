/* eslint-disable react/jsx-no-bind */
import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { DateRangePicker } from 'react-dates';
import { translate } from 'react-i18next';
import { compose } from 'redux';
import { useHistory, useParams } from 'react-router-dom';
import {
  Button,
  Card,
  CardBody,
  CardTitle,
  Col,
  FormGroup,
  InputGroup,
  Label,
  Row
} from 'reactstrap';
import Loader from 'react-loader-advanced';
import { Loader as LoaderAnim } from 'react-loaders';
import { useDrop } from 'react-dnd';
import { IoIosCloseCircleOutline } from 'react-icons/io';

import {
  addEditAnalyticsAPI,
  getAnalyticDetailsAPI
} from '../../../../../api/analytics/createAnalytics';

import { TYPES } from '../../../../../redux/modules/appState/sidebar';
import reduxConnect from '../../../../../redux/utils/connect';
import useIsMounted from '../../../../common/hooks/useIsMounted';
import { subChartCategories } from './ShowCharts';
import { getMomentObject, setDocumentData } from '../../../../../common/helper';

const initialState = {
  feeds: [],
  startDate: null,
  endDate: null
};

const spinner = <LoaderAnim color="#ffffff" type="ball-pulse" />;

function CreateAnalysisSubTab({ t, actions }) {
  const isMounted = useIsMounted();
  const history = useHistory();
  const { id } = useParams();
  const [form, setForm] = useState(initialState);
  const [error, setError] = useState();
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(!!id);
  const [focusedInput, setFocusedInput] = useState();
  const [{ canDrop, isOver }, drop] = useDrop({
    accept: [TYPES.FEED, TYPES.CLIP_ARTICLE],
    drop: droppedFeeds,
    canDrop: canDroppable,
    collect: (monitor) => ({
      isOver: monitor.isOver(),
      canDrop: monitor.canDrop()
    })
  });

  function getAnalyticData() {
    setFetching(true);
    getAnalyticDetailsAPI(id).then((res) => {
      if (!isMounted.current) {
        return;
      }
      if (res.error || !res.data || !res.data.context) {
        setFetching(false);
        res.data
          ? actions.addAlert(res.data)
          : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
        history.push('/app/analyze/saved');
        return;
      }

      const { context } = res.data;
      const date = context && context.rawFilters && context.rawFilters.date;
      setForm({
        feeds: context.feeds.map((item) => ({
          feed: { name: item.name },
          id: item.id
        })),
        startDate: getMomentObject(date && date.start),
        endDate: getMomentObject(date && date.end)
      });
      setFetching(false);
    });
  }

  useEffect(() => {
    setDocumentData('title', `${id ? 'Update' : 'Create'} Analysis | Analyze`);
    return () => {
      setDocumentData('title');
    };
  }, []);

  useEffect(() => {
    if (id) {
      getAnalyticData();
    }
  }, [id]);

  function canDroppable(item) {
    if (form.feeds.find((val) => val.id === item.id)) {
      return false;
    }

    return true;
  }

  function droppedFeeds(item) {
    if (form.feeds.find((val) => val.id === item.id)) {
      return;
    }

    setForm((prev) => ({ ...prev, feeds: [...prev.feeds, item] }));
  }

  function removeFeeds(id) {
    setForm((prev) => {
      const modifiedFeeds = form.feeds.filter((val) => val.id !== id);
      return { ...prev, feeds: modifiedFeeds };
    });
  }

  const isActive = canDrop && isOver;
  function handleSubmit() {
    const isValid = Object.values(form).every((value) =>
      value ? (Array.isArray(value) ? value.length > 0 : true) : false
    );
    if (!isValid) {
      return setError(t('common:alerts.error.requiredInfo'));
    }

    setError(false);
    setLoading(true);
    addEditAnalyticsAPI(form, id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.id) {
        // on error
        setLoading(false);
        setError(res.errorMessage);
        return;
      }

      actions.resetAlertChart();
      setLoading(false);

      history.push(`/app/analyze/${res.data.id}/${subChartCategories[0].path}`);
    });
  }

  function handleDateChange({ startDate, endDate }) {
    setForm((prev) => ({ ...prev, startDate, endDate }));
  }

  function onFocusChange(focus) {
    setFocusedInput(focus);
  }

  function isOutsideRange() {
    return false;
  }

  const isRTL = document.documentElement.dir === 'rtl';
  return (
    <Card className="mb-3">
      <Loader message={spinner} show={fetching}>
        <CardBody>
          <CardTitle>
            {id ? t('analyzeTab.updateDetails') : t('analyzeTab.enterDetails')}
          </CardTitle>
          <Row>
            <Col sm="12">
              <FormGroup data-tour="drop-feeds-box">
                <div>
                  {form.feeds.length > 0 && (
                    <div className="mb-3">
                      <Label>{t('analyzeTab.selectedFeeds')}</Label>
                      <div>
                        {form.feeds.map((item) => (
                          <div
                            key={item.id}
                            className="bg-light d-inline d-inline-flex align-items-center mr-2 p-2 text-dark"
                          >
                            <p>{item.feed.name}</p>
                            <button
                              className="btn p-0"
                              onClick={function () {
                                removeFeeds(item.id);
                              }}
                            >
                              <IoIosCloseCircleOutline
                                size={22}
                                className="text-danger ml-2"
                              />
                            </button>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                  <Label>{t('analyzeTab.selectFeeds')}</Label>
                  <div ref={drop} className="dropzone-wrapper">
                    <div>
                      <div className="dropzone-content">
                        <p>
                          {isActive
                            ? t('analyzeTab.releaseDesc')
                            : t('analyzeTab.dropDesc')}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </FormGroup>
              <FormGroup data-tour="analytics-data-range">
                <Label className="mr-sm-2">{t('analyzeTab.dateRange')}</Label>
                <InputGroup>
                  <DateRangePicker
                    startDateId="startDate"
                    endDateId="endDate"
                    startDate={form.startDate}
                    endDate={form.endDate}
                    onDatesChange={handleDateChange}
                    focusedInput={focusedInput}
                    onFocusChange={onFocusChange}
                    displayFormat="MM/DD/YYYY"
                    startDatePlaceholderText={t('analyzeTab.startDatePlaceholder')}
                    endDatePlaceholderText={t('analyzeTab.endDatePlaceholder')}
                    numberOfMonths={1}
                    isOutsideRange={isOutsideRange}
                    isRTL={isRTL}
                  />
                </InputGroup>
              </FormGroup>
              {error && <div className="text-danger mb-2">{error}</div>}
              <Button
                className="mb-2 mr-2 btn-icon"
                color="primary"
                disabled={loading}
                data-tour="create-analytics-button"
                onClick={handleSubmit}
              >
                {loading
                  ? 'Loading...'
                  : id
                  ? t('analyzeTab.updateBtn')
                  : t('analyzeTab.createBtn')}
              </Button>
            </Col>
          </Row>
        </CardBody>
      </Loader>
    </Card>
  );
}

CreateAnalysisSubTab.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object
};

const applyDecorators = compose(
  reduxConnect(),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(CreateAnalysisSubTab);
