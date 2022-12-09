import React from 'react';
import PropTypes from 'prop-types';
import { faSpinner } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import i18n from '../../i18n';

function Loading({
  show = true,
  message = i18n.t('common:commonWords.loading')
}) {
  if (!show) {
    return null;
  }

  return (
    <div className="d-flex flex-column align-items-center justify-content-center text-muted p-5">
      <span className="mb-2">
        <FontAwesomeIcon icon={faSpinner} size="2x" pulse />
      </span>
      <p>{message}</p>
    </div>
  );
}

Loading.propTypes = {
  show: PropTypes.bool,
  message: PropTypes.string
};

export default Loading;
