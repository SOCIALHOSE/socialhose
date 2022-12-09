import React from 'react';
import PropTypes from 'prop-types';
import { IoIosInformationCircle } from 'react-icons/io';
import i18n from '../../i18n';

function NoRecords({
  show = true,
  message = i18n.t('common:messages.noResults')
}) {
  if (!show) {
    return null;
  }

  return (
    <div className="d-flex flex-column align-items-center justify-content-center text-muted p-5">
      <IoIosInformationCircle className="mb-2" fontSize="32px" />
      <p>{message}</p>
    </div>
  );
}

NoRecords.propTypes = {
  show: PropTypes.bool,
  message: PropTypes.string
};

export default NoRecords;
