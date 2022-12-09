import React from 'react';
import PropTypes from 'prop-types';
import { FormGroup, Label, CustomInput } from 'reactstrap';
import { Interpolate, translate } from 'react-i18next';

// need changes: err cannot be removed once triggered
function Checkbox({
  t,
  title,
  hideTitle,
  name,
  formGroupClass,
  required,
  trueValue,
  value,
  disabled,
  error,
  description,
  handleChange
}) {
  function onChange(e) {
    const { name, checked } = e.target;
    let oppValue = null;
    if (typeof trueValue === 'boolean') oppValue = false;
    else if (typeof trueValue === 'number') oppValue = 0;
    handleChange(name, checked ? trueValue : oppValue);
  }

  return (
    <FormGroup className={formGroupClass}>
      {!hideTitle && (
        <Label>
          {title}
          {required ? <span className="text-danger">*</span> : null}
        </Label>
      )}
      <CustomInput
        id={name}
        type="checkbox"
        checked={trueValue === value}
        title={title}
        name={name}
        value={value}
        disabled={disabled}
        label={description}
        invalid={error}
        onChange={onChange}
      />
      {error === true ? (
        <span className="text-danger">
          <Interpolate t={t} i18nKey="messages.selectMsg" title={title} />
        </span>
      ) : (
        <span className="text-danger">{error}</span>
      )}
    </FormGroup>
  );
}

Checkbox.defaultProps = {
  handleChange: () => {},
  trueValue: true,
  disabled: false,
  hideTitle: false,
  formGroupClass: ''
};

Checkbox.propTypes = {
  t: PropTypes.func,
  title: PropTypes.string,
  name: PropTypes.string.isRequired,
  type: PropTypes.string,
  formGroupClass: PropTypes.string,
  value: PropTypes.bool,
  hideTitle: PropTypes.bool,
  trueValue: PropTypes.any,
  description: PropTypes.any,
  required: PropTypes.bool,
  disabled: PropTypes.bool,
  error: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
  handleChange: PropTypes.func.isRequired
};

export default React.memo(translate(['common'], { wait: true })(Checkbox));
