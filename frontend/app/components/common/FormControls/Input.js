import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import {
  Input as ReactInput,
  FormGroup,
  Label,
  FormText,
  InputGroup
} from 'reactstrap';
import { translate } from 'react-i18next';

function Input({
  t,
  title,
  name,
  required,
  disabled,
  value,
  type,
  options,
  placeholder,
  error,
  description,
  regex,
  handleChange,
  handleValidation,
  validationMessage,
  inputGroupAddon
}) {
  function onChange(e) {
    handleChange(e.target.name.trim(''), e.target.value);
  }

  function onValidate(e) {
    if (!handleValidation) return;
    const { value, name } = e.target;
    let errorMsg = required ? null : undefined;
    if (!value.replace(/\s/g, '').length && required) {
      errorMsg = true;
    } else if (value && regex && !regex.test(value)) {
      errorMsg = validationMessage || t('messages.invalidMsg', { title });
    }

    handleValidation(name, errorMsg);
  }

  let optionsJSX;
  if (type === 'select' && Array.isArray(options)) {
    optionsJSX = [
      <option key="empty" value="">
        {t('messages.dropdownValue0', { title })}
      </option>
    ];
    options.map((option) =>
      optionsJSX.push(
        <option key={option.value} value={option.value}>
          {option.label}
        </option>
      )
    );
  }

  return (
    <FormGroup>
      <Label>
        {title}
        {required ? <span className="text-danger">*</span> : null}
      </Label>
      {inputGroupAddon ? (
        <Fragment>
          <InputGroup>
            <ReactInput
              title={title}
              type={type}
              name={name}
              value={value}
              placeholder={placeholder}
              invalid={!!error}
              onChange={onChange}
              onBlur={onValidate}
              disabled={disabled}
              children={optionsJSX}
            />
            {inputGroupAddon}
          </InputGroup>
        </Fragment>
      ) : (
        <ReactInput
          title={title}
          type={type}
          name={name}
          value={value}
          placeholder={placeholder}
          invalid={!!error}
          onChange={onChange}
          onBlur={onValidate}
          disabled={disabled}
          children={optionsJSX}
        />
      )}
      {error === true ? (
        <span className="text-danger">{t('messages.inputMsg', { title })}</span>
      ) : (
        <span className="text-danger">{error}</span>
      )}
      {description && <FormText>{description}</FormText>}
    </FormGroup>
  );
}

Input.defaultProps = {
  handleChange: () => {},
  handleValidation: () => {},
  disabled: false
};

Input.propTypes = {
  t: PropTypes.func,
  title: PropTypes.string,
  name: PropTypes.string.isRequired,
  type: PropTypes.string,
  value: PropTypes.string,
  placeholder: PropTypes.string,
  validationMessage: PropTypes.string,
  required: PropTypes.bool,
  disabled: PropTypes.bool,
  error: PropTypes.oneOfType([
    PropTypes.bool,
    PropTypes.object,
    PropTypes.string
  ]),
  regex: PropTypes.object,
  options: PropTypes.any,
  description: PropTypes.any,
  inputGroupAddon: PropTypes.any,
  handleChange: PropTypes.func,
  handleValidation: PropTypes.func
};

export default React.memo(translate(['common'], { wait: true })(Input));
