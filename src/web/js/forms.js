
(() => {
  const FORM_SELECTOR = '.forms-widget__form';

  const initPhoneMask = (input) => {
    const mask = input.dataset.formsMask || '';
    if (!mask.includes('9')) {
      return;
    }

    const placeholderPositions = [...mask].reduce((positions, char, index) => {
      if (char === '9') {
        positions.push(index);
      }
      return positions;
    }, []);

    const placeholdersCount = placeholderPositions.length;

    const normalizeDigits = (value) => {
      let digits = String(value || '').replace(/\D/g, '');

      if (mask.startsWith('+7') && placeholdersCount === 10 && digits.length === 11 && (digits.startsWith('7') || digits.startsWith('8'))) {
        digits = digits.slice(1);
      }

      return digits.slice(0, placeholdersCount);
    };

    const format = (digits) => {
      let index = 0;
      return mask.replace(/9/g, () => digits[index++] ?? '_');
    };

    const getDigits = () => input.dataset.formsDigits || '';

    const setDigits = (value, showMask = true) => {
      const digits = normalizeDigits(value);
      input.dataset.formsDigits = digits;
      input.value = digits.length || showMask ? format(digits) : '';
      return digits;
    };

    const setCaret = () => {
      const digits = getDigits();
      const caretIndex = digits.length >= placeholderPositions.length
        ? placeholderPositions[placeholderPositions.length - 1] + 1
        : placeholderPositions[digits.length];

      requestAnimationFrame(() => {
        input.setSelectionRange(caretIndex, caretIndex);
      });
    };

    const appendDigit = (digit) => {
      const digits = getDigits();
      if (digits.length >= placeholdersCount) {
        return;
      }
      setDigits(digits + digit, true);
      setCaret();
    };

    const removeDigit = () => {
      const digits = getDigits();
      setDigits(digits.slice(0, -1), true);
      setCaret();
    };

    setDigits(input.value, false);

    input.addEventListener('focus', () => {
      setDigits(getDigits(), true);
      setCaret();
    });

    input.addEventListener('click', () => {
      if (document.activeElement === input) {
        setCaret();
      }
    });

    input.addEventListener('keydown', (event) => {
      if (event.ctrlKey || event.metaKey || event.altKey) {
        return;
      }

      if (event.key === 'Tab') {
        return;
      }

      if (/^\d$/.test(event.key)) {
        event.preventDefault();
        appendDigit(event.key);
        return;
      }

      if (event.key === 'Backspace' || event.key === 'Delete') {
        event.preventDefault();
        removeDigit();
        return;
      }

      if (['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) {
        event.preventDefault();
        setCaret();
      }
    });

    input.addEventListener('paste', (event) => {
      event.preventDefault();
      const pasted = event.clipboardData?.getData('text') || '';
      setDigits(pasted, true);
      setCaret();
    });

    input.addEventListener('input', () => {
      setDigits(input.value, true);
      setCaret();
    });

    input.addEventListener('blur', () => {
      const digits = getDigits();
      if (!digits.length) {
        input.value = '';
      }
    });
  };

  const setFieldError = (wrap, fieldName, message) => {
    wrap.classList.add('is-invalid');
    wrap.querySelectorAll('.forms-control').forEach((input) => input.classList.add('is-invalid'));
    const error = wrap.querySelector(`[data-forms-error="${fieldName}"]`);
    if (error) {
      error.textContent = message;
    }
  };

  const clearFieldError = (wrap, fieldName) => {
    wrap.classList.remove('is-invalid');
    wrap.querySelectorAll('.forms-control').forEach((input) => input.classList.remove('is-invalid'));
    const error = wrap.querySelector(`[data-forms-error="${fieldName}"]`);
    if (error) {
      error.textContent = '';
    }
  };

  const validateWrap = (wrap) => {
    const fieldName = wrap.dataset.formsFieldWrap;
    const fieldType = wrap.dataset.formsFieldType || 'text';
    const required = wrap.dataset.formsRequired === '1';
    const input = wrap.querySelector('[data-forms-field]');
    const label = input?.dataset.formsLabel || 'Поле';

    clearFieldError(wrap, fieldName);

    if (!input && fieldType !== 'radio') {
      return true;
    }

    if (fieldType === 'checkbox') {
      if (required && !input.checked) {
        setFieldError(wrap, fieldName, fieldName === 'forms_personal_agreement'
          ? 'Необходимо дать согласие на обработку персональных данных.'
          : `Отметьте поле "${label}".`);
        return false;
      }
      return true;
    }

    if (fieldType === 'radio') {
      const checked = wrap.querySelector('input[type="radio"]:checked');
      if (required && !checked) {
        setFieldError(wrap, fieldName, `Выберите вариант в поле "${label}".`);
        return false;
      }
      return true;
    }

    const rawValue = input.value ?? '';
    const value = rawValue.trim();

    if (required && !value) {
      setFieldError(wrap, fieldName, `Заполните поле "${label}".`);
      return false;
    }

    if (!value) {
      return true;
    }

    if (fieldType === 'email') {
      const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
      if (!ok) {
        setFieldError(wrap, fieldName, 'Укажите корректный e-mail.');
        return false;
      }
    }

    if (fieldType === 'number') {
      if (Number.isNaN(Number(value))) {
        setFieldError(wrap, fieldName, `Поле "${label}" должно быть числом.`);
        return false;
      }
    }

    if (fieldType === 'phone') {
      const digits = value.replace(/\D/g, '');
      const hasMaskGaps = value.includes('_');
      if ((required && !value) || ((digits.length > 0 || value !== '') && hasMaskGaps)) {
        setFieldError(wrap, fieldName, 'Введите корректный телефон.');
        return false;
      }
    }

    return true;
  };

  const initForm = (form) => {
    const submitButton = form.querySelector('.forms-widget__submit');

    form.querySelectorAll('input[data-forms-type="phone"]').forEach(initPhoneMask);

    form.querySelectorAll('[data-forms-field-wrap]').forEach((wrap) => {
      wrap.addEventListener('input', () => validateWrap(wrap));
      wrap.addEventListener('change', () => validateWrap(wrap));
    });

    form.addEventListener('submit', (event) => {
      let valid = true;
      form.querySelectorAll('[data-forms-field-wrap]').forEach((wrap) => {
        if (!validateWrap(wrap)) {
          valid = false;
        }
      });

      if (!valid) {
        event.preventDefault();
        const firstError = form.querySelector('.forms-field.is-invalid');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
          const firstInput = firstError.querySelector('input, textarea, select');
          if (firstInput) {
            firstInput.focus();
          }
        }
        return;
      }

      if (submitButton) {
        submitButton.classList.add('is-loading');
        submitButton.disabled = true;
        submitButton.setAttribute('aria-busy', 'true');
      }
    });
  };

  const bootstrap = () => {
    document.querySelectorAll(FORM_SELECTOR).forEach((form) => {
      if (form.dataset.formsReady === '1') {
        return;
      }
      form.dataset.formsReady = '1';
      initForm(form);
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrap);
  } else {
    bootstrap();
  }

  window.addEventListener('pageshow', () => {
    document.querySelectorAll('.forms-widget__submit.is-loading').forEach((button) => {
      button.classList.remove('is-loading');
      button.disabled = false;
      button.removeAttribute('aria-busy');
    });
  });
})();
