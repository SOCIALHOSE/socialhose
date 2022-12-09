import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Col, CustomInput, FormGroup } from 'reactstrap';

export class DuplicatesTab extends React.Component {
  static propTypes = {
    includeDuplicates: PropTypes.bool.isRequired,
    toggleIncludeDuplicates: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  render() {
    const { t } = this.props;

    return (
      <Col sm={12}>
        <FormGroup>
          <CustomInput
            className="checkbox-input-hidden"
            type="checkbox"
            id="duplicates-check"
            checked={this.props.includeDuplicates}
            onChange={this.props.toggleIncludeDuplicates}
            label={t('searchTab.searchBySection.duplicates.includeDuplicates')}
          />
        </FormGroup>
      </Col>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(DuplicatesTab);
