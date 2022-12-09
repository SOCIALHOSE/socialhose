import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Col, CustomInput, FormGroup } from 'reactstrap';

function ExtrasTab({ t, hasImages, toggleHasImages }) {
  return (
    <Col sm={12}>
      <FormGroup>
        <CustomInput
          id="has-images-check"
          type="checkbox"
          className="d-flex"
          checked={hasImages}
          label={t('searchTab.searchBySection.extras.hasImages')}
          onChange={toggleHasImages}
        />
      </FormGroup>
    </Col>
  );
}

ExtrasTab.propTypes = {
  hasImages: PropTypes.bool.isRequired,
  toggleHasImages: PropTypes.func.isRequired,
  t: PropTypes.func.isRequired
};

export default translate(['tabsContent'], { wait: true })(ExtrasTab);
