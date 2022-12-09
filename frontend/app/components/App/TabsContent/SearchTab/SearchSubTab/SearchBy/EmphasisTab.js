import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Col, FormGroup, Input, Label } from 'reactstrap';

export class EmphasisTab extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    include: PropTypes.string.isRequired,
    exclude: PropTypes.string.isRequired,
    setHeadlineIncluded: PropTypes.func.isRequired,
    setHeadlineExcluded: PropTypes.func.isRequired
  };

  setHeadInclude = (e) => {
    const headline = e.target.value;
    this.props.setHeadlineIncluded(headline);
  };

  setHeadExclude = (e) => {
    const headline = e.target.value;
    this.props.setHeadlineExcluded(headline);
  };

  render() {
    const { t, include, exclude } = this.props;

    return (
      <Fragment>
        <Col sm="6">
          <FormGroup>
            <Label>
              {t('searchTab.searchBySection.emphasis.headlineLabel')}{' '}
              {t('searchTab.searchBySection.emphasis.include')}
            </Label>
            <Input type="text" value={include} onChange={this.setHeadInclude} />
          </FormGroup>
        </Col>
        <Col sm="6">
          <FormGroup>
            <Label>
              {t('searchTab.searchBySection.emphasis.headlineLabel')}{' '}
              {t('searchTab.searchBySection.emphasis.exclude')}
            </Label>
            <Input type="text" value={exclude} onChange={this.setHeadExclude} />
          </FormGroup>
        </Col>
      </Fragment>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(EmphasisTab);
