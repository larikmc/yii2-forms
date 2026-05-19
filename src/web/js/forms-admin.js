document.addEventListener('click', async function (e) {
  const btn = e.target.closest('[data-forms-copy]');
  if (!btn) return;
  const target = document.querySelector(btn.getAttribute('data-forms-copy'));
  if (!target) return;
  try { await navigator.clipboard.writeText(target.textContent || target.value || ''); btn.textContent='Скопировано'; setTimeout(()=>btn.textContent='Скопировать',1200); } catch (_) {}
});
