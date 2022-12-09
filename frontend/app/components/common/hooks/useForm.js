import { useState, useCallback } from 'react';
import { cloneDeep } from 'lodash';

function useForm({ errors: initialErrors, ...rest }) {
  const [form, setForm] = useState(rest);
  const [errors, setErrors] = useState(initialErrors);

  const handleChange = useCallback((name, value, err = undefined) => {
    setForm((form) => ({
      ...form,
      [name]: value
    }));

    if (errors) {
      setErrors((errors) => ({
        ...errors,
        [name]: err !== undefined ? err : errors[name]
      }));
    }
  }, []);

  const handleValidation = useCallback((name, err) => {
    setErrors((errors) => ({
      ...errors,
      [name]: err
    }));
  }, []);

  const validateSubmit = useCallback(() => {
    let failed;
    for (let val in errors) {
      const fieldError = errors[val];
      if (fieldError) {
        failed = true;
      } else if (fieldError === null && !form[val] && form[val] !== 0) {
        failed = true;
        handleValidation(val, true);
      }
    }
    if (failed) {
      return false;
    } else {
      return cloneDeep(form);
    }
  }, [form, errors, handleValidation]);

  function resetForm(values = {}) {
    // eslint-disable-next-line no-unused-vars
    const { errors, ...formValues } = values;
    setForm(formValues || rest);
    setErrors(initialErrors);
  }

  return {
    form,
    handleChange,
    handleValidation,
    setForm,
    validateSubmit,
    errors,
    resetForm
  };
}

export default useForm;
