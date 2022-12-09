import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import TopBar from './TopBar';
import EmailsTable from './EmailsTable';
import reduxConnect from '../../../../../redux/utils/connect';
import AlertForm from './AlertForm';
import Navigation from './Navigation';
import FiltersTable from './FiltersTable';
import FiltersTopBar from './FiltersTopBar';
import { EMAILS_SUBSCREENS } from '../../../../../redux/modules/appState/share/tabs';
import { setDocumentData } from '../../../../../common/helper';

class ManageEmailsSubTab extends React.Component {
  static propTypes = {
    shareState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

  componentDidMount() {
    setDocumentData('title', 'Manage Recipients | Share')
  }

  componentWillUnmount() {
    setDocumentData('title')
  }

  render() {
    const { shareState, actions } = this.props;
    const { subScreenVisible } = shareState.tabs.emails;

    return (
      <div className="notifications-tab">
        {subScreenVisible === EMAILS_SUBSCREENS.EMAILS_TABLE && (
          <div>
            <TopBar tableState={shareState.tables.emails} actions={actions} />
            <EmailsTable
              tableState={shareState.tables.emails}
              actions={actions}
              tableActions={actions.shareTables.emails}
              deleteSingleText="email"
              deleteMultipleText="emails"
            />
          </div>
        )}

        {(subScreenVisible === EMAILS_SUBSCREENS.ALERT_FORM ||
          subScreenVisible === EMAILS_SUBSCREENS.NEWSLETTER_FORM) && (
          <Navigation actions={actions} />
        )}

        {subScreenVisible === EMAILS_SUBSCREENS.ALERT_FORM && (
          <AlertForm
            state={shareState.forms.alert}
            switchShareSubScreen={actions.switchShareSubScreen}
            actions={actions.shareForms.alert}
          />
        )}

        {/* {subScreenVisible === EMAILS_SUBSCREENS.NEWSLETTER_FORM &&
          <NewsletterForm
            state={shareState.forms.newsletter}
            switchShareSubScreen={actions.switchShareSubScreen}
            actions={actions.shareForms.newsletter}
          />
        } */}

        {subScreenVisible === EMAILS_SUBSCREENS.FILTERS_TABLE && (
          <Fragment>
            <FiltersTopBar
              actions={actions}
              filterType={shareState.tables.emailFilters.filterType}
            />
            <FiltersTable
              actions={actions}
              tableState={shareState.tables.emailFilters}
              tableActions={actions.shareTables.emailFilters}
            />
          </Fragment>
        )}
      </div>
    );
  }
}

export default reduxConnect('shareState', ['appState', 'share'])(
  ManageEmailsSubTab
);
