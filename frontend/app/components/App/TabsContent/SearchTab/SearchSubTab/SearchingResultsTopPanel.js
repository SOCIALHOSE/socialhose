import React from 'react';
import PropTypes from 'prop-types';
import { ButtonGroup, Button, CustomInput } from 'reactstrap';
import { translate } from 'react-i18next';

export class SearchingResultsTopPanel extends React.Component {
  static propTypes = {
    noRecords: PropTypes.bool,
    selectedArticles: PropTypes.array.isRequired,
    searchResultsCount: PropTypes.number.isRequired,
    selectAllArticles: PropTypes.func.isRequired,
    showDeleteArticlesPopup: PropTypes.func.isRequired,
    showEmailArticlesPopup: PropTypes.func.isRequired,
    showClipArticlesPopup: PropTypes.func.isRequired,
    isRefinePanelVisible: PropTypes.bool.isRequired,
    toggleRefinePanel: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  onShowClick = (e) => {
    e.preventDefault();
    this.props.toggleRefinePanel();
  };

  selectAllArticles = (e) => {
    const isChecked = e.target.checked;
    if (this.props.searchResultsCount > 0) {
      this.props.selectAllArticles(isChecked);
    }
  };

  showDeleteArticlesPopup = () => {
    if (this.props.selectedArticles.length > 0) {
      this.props.showDeleteArticlesPopup(this.props.selectedArticles);
    }
  };

  showEmailArticlesPopup = () => {
    if (this.props.selectedArticles.length > 0) {
      this.props.showEmailArticlesPopup(this.props.selectedArticles);
    }
  };

  showClipArticlesPopup = () => {
    if (this.props.selectedArticles.length > 0) {
      this.props.showClipArticlesPopup(this.props.selectedArticles);
    }
  };

  render() {
    const { t, searchResultsCount, noRecords } = this.props;
    const chosenArticlesCount = this.props.selectedArticles.length;
    const isAllArticlesChosen =
      this.props.searchResultsCount > 0
        ? searchResultsCount === chosenArticlesCount
        : false;

    if (noRecords) {
      return null;
    }

    return (
      <div className="d-flex justify-content-end mb-3 mb-md-0">
        <ButtonGroup>
          <Button color="light">
            <CustomInput
              id="toggle-all-results"
              type="checkbox"
              checked={isAllArticlesChosen}
              onChange={this.selectAllArticles}
            />
          </Button>

          {/* <Button color="secondary">
            <i className="fa fa-tag mr-2"> </i>
            {t('searchTab.tagBtn')}
          </Button> */}

          <Button color="secondary" onClick={this.showClipArticlesPopup}>
            <i className="fa fa-scissors mr-2"> </i>
            {t('searchTab.clipBtn')}
          </Button>
          <Button color="secondary" onClick={this.showEmailArticlesPopup}>
            <i className="fa fa-envelope-o mr-2"> </i>
            {t('searchTab.emailBtn')}
          </Button>
          <Button color="secondary" onClick={this.showDeleteArticlesPopup}>
            <i className="fa fa-trash mr-2"> </i>
            {t('searchTab.deleteBtn')}
          </Button>
        </ButtonGroup>

        {!this.props.isRefinePanelVisible && (
          <Button
            color="light"
            title="Show refine panel"
            className="btn-icon ml-3"
            onClick={this.onShowClick}
          >
            <i className="pe-7s-filter btn-icon-wrapper"></i>
            {t('searchTab.filter')}
          </Button>
        )}
      </div>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(
  SearchingResultsTopPanel
);
