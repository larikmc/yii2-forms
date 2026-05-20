
(() => {
  const activeClass = 'forms-modal--open';
  const bodyClass = 'forms-modal-body-lock';

  function getModal(trigger) {
    const selector = trigger.getAttribute('data-forms-modal-open');
    return selector ? document.querySelector(selector) : null;
  }

  function openModal(modal) {
    if (!modal) return;
    modal.classList.add(activeClass);
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add(bodyClass);
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove(activeClass);
    modal.setAttribute('aria-hidden', 'true');
    if (!document.querySelector('.forms-modal.' + activeClass)) {
      document.body.classList.remove(bodyClass);
    }
  }

  document.addEventListener('click', (event) => {
    const openTrigger = event.target.closest('[data-forms-modal-open]');
    if (openTrigger) {
      event.preventDefault();
      openModal(getModal(openTrigger));
      return;
    }

    const closeTrigger = event.target.closest('[data-forms-modal-close]');
    if (closeTrigger) {
      event.preventDefault();
      closeModal(closeTrigger.closest('.forms-modal'));
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    const modal = document.querySelector('.forms-modal.' + activeClass);
    if (modal) {
      closeModal(modal);
    }
  });
})();
