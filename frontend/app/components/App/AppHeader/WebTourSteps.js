import React from 'react';
import i18n from '../../../i18n';
import { Trans } from 'react-i18next';

const baseKey = 'tabsContent:webtour';

const steps = [
  {
    selector: '',
    content: i18n.t(`${baseKey}.search.start`)
  },
  {
    selector: '[data-tour="left-panel"]',
    content: i18n.t(`${baseKey}.search.feedsView`),
    resizeObservables: ['[data-tour="left-panel"]'],
    needSidebar: true,
    stepInteraction: false
  },
  {
    selector: '[data-tour="app-header-left"]',
    content: () => (
      <Trans i18nKey={`${baseKey}.search.mainTabs`}>
        There are 3 main pages: <strong>Search</strong> to find content,
        <strong>Analyze</strong> to generate reports, and <strong>Share</strong>
        to distribute findings via alerts or webfeeds.
      </Trans>
    ),
    onlyWeb: true,
    stepInteraction: false
  },
  {
    selector: '[data-tour="app-header-user-settings"]',
    content: i18n.t(`${baseKey}.search.userSettings`),
    stepInteraction: false
  },
  {
    selector: '[data-tour="search-licenses"]',
    content: i18n.t(`${baseKey}.search.license`),
    stepInteraction: false
  },
  {
    selector: '[data-tour="input-field-search"]',
    content: () => (
      <p>
        <Trans i18nKey={`${baseKey}.search.searchField`}>
          A simple boolean search looks like this:
          <strong>BMW AND Texas</strong>. Which will find all mentions of “bmw”
          and "texas”.
        </Trans>
      </p>
    )
  },
  {
    selector: '[data-tour="select-date-range"]',
    content: i18n.t(`${baseKey}.search.dateRange`),
    stepInteraction: false
  },
  {
    selector: '[data-tour="select-media-types"]',
    content: i18n.t(`${baseKey}.search.mediaChannels`)
  },
  {
    selector: '[data-tour="advanced-search"]',
    content: () => (
      <Trans i18nKey={`${baseKey}.search.advancedSearch`}>
        Click on <strong>Advanced Search</strong> to uncover the different
        options for your search.
      </Trans>
    ),
    resizeObservables: ['[data-tour="advanced-search"]']
  },
  {
    selector: '[data-tour="advanced-search"]',
    content: () => (
      <Trans i18nKey={`${baseKey}.search.emphasis`}>
        <strong>Emphasis:</strong> Include or exclude specific words or phrases
        in the headline of a news article or a blog post.
      </Trans>
    ),
    resizeObservables: ['[data-tour="advanced-search-content"]']
  },
  {
    selector: '[data-tour="advanced-search"]',
    content: () => (
      <Trans i18nKey={`${baseKey}.search.languages`}>
        <strong>Languages:</strong> Capture the content that is tagged with the
        following language(s).
      </Trans>
    ),
    resizeObservables: ['[data-tour="advanced-search-content"]']
  },
  {
    selector: '[data-tour="advanced-search"]',
    content: () => (
      <Trans i18nKey={`${baseKey}.search.locations`}>
        <strong>Locations:</strong> Include or exclude content that is geotagged
        with the following countries or US States.
      </Trans>
    ),
    resizeObservables: ['[data-tour="advanced-search-content"]']
  },
  {
    selector: '[data-tour="advanced-search"]',
    content: () => (
      <Trans i18nKey={`${baseKey}.search.extras`}>
        <strong>Extras:</strong> Only show posts with images.
      </Trans>
    ),
    resizeObservables: ['[data-tour="advanced-search-content"]']
  },
  /*   {
    selector: '[data-tour="search-button"]',
    content: () => (
      <Fragment>
        Click <strong>Search icon</strong>.
      </Fragment>
    )
  }, */
  {
    selector: '[data-tour="search-buttons"]',
    content: i18n.t(`${baseKey}.search.saveSearch`),
    stepInteraction: false
  }
];

const analyticsSteps = [
  {
    selector: '',
    content: i18n.t(`${baseKey}.analytics.start`)
  },
  {
    selector: '[data-tour="left-panel"]',
    content: i18n.t(`${baseKey}.analytics.dragFeed`),
    resizeObservables: ['[data-tour="left-panel"]'],
    needSidebar: true
  },
  {
    selector: '[data-tour="drop-feeds-box"]',
    highlightedSelectors: ['[data-tour="left-panel"]'],
    content: i18n.t(`${baseKey}.analytics.drop`)
  },
  {
    selector: '[data-tour="analytics-data-range"]',
    content: i18n.t(`${baseKey}.analytics.dateRange`),
    observe: '.DateRangePickerInput'
  },
  {
    selector: '[data-tour="create-analytics-button"]',
    content: i18n.t(`${baseKey}.analytics.create`)
  }
];

const tourPages = [
  {
    translateKey: 'HowToSearch',
    name: 'How to Search',
    icon: 'pe-7s-search',
    to: '/app/search/search',
    showOn: '/app/search/search',
    content: steps
  },
  {
    translateKey: 'HowToAnalyze',
    name: 'How to Analyze',
    icon: 'pe-7s-graph1',
    to: '/app/analyze/create',
    showOn: '/app/analyze',
    content: analyticsSteps
  }
];

export default tourPages;
