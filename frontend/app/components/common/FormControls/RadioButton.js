import React from 'react';
import PropTypes from 'prop-types';
import { FormGroup, Label, CustomInput } from 'reactstrap';
import { translate } from 'react-i18next';

function RadioButton({
  t,
  title,
  name,
  required,
  value,
  disabled,
  error,
  inline,
  options,
  valueKey,
  labelKey,
  formClass,
  handleChange
}) {
  function onChange(e, value) {
    let errorMsg = required ? null : undefined;
    if (!value && required) {
      errorMsg = t('messages.selectMsg', { title: name });
    } else {
      errorMsg = false;
    }
    handleChange(name, value, errorMsg);
  }

  return (
    <FormGroup className={formClass}>
      <Label className={inline ? 'mr-4' : ''}>
        {title}
        {inline ? ':' : null}
        {required ? <span className="text-danger">*</span> : null}
      </Label>
      {options.map((o, i) => (
        <CustomInput
          key={`${name}${i}`}
          id={`${name}${i}`}
          type="radio"
          inline={inline}
          checked={o[valueKey] === value}
          title={title}
          name={name}
          disabled={disabled}
          label={o[labelKey]}
          invalid={error}
          onChange={function (e) {
            onChange(e, o[valueKey]);
          }}
        />
      ))}
      {error === true ? (
        <span className="text-danger">{t('messages.inputMsg', { title })}</span>
      ) : (
        <span className="text-danger">{error}</span>
      )}
    </FormGroup>
  );
}

RadioButton.defaultProps = {
  options: [],
  valueKey: 'value',
  labelKey: 'label',
  value: null,
  handleChange: () => {},
  handleValidation: () => {},
  disabled: false
};

RadioButton.propTypes = {
  t: PropTypes.func,
  title: PropTypes.string,
  name: PropTypes.string.isRequired,
  type: PropTypes.string,
  value: PropTypes.oneOfType([PropTypes.bool, PropTypes.string]),
  formClass: PropTypes.string,
  options: PropTypes.array,
  labelKey: PropTypes.string,
  valueKey: PropTypes.string,
  required: PropTypes.bool,
  disabled: PropTypes.bool,
  inline: PropTypes.bool,
  error: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
  handleChange: PropTypes.func.isRequired
};

export default React.memo(
  translate(['tabsContent'], { wait: true })(RadioButton)
);
