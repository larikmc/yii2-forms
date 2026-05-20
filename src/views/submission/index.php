<?php
use larikmc\forms\assets\FormsAdminAsset;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

FormsAdminAsset::register($this);

$this->registerCss(<<<CSS
.forms-admin-popup[hidden]{display:none!important;}
.forms-admin-popup{position:fixed;inset:0;z-index:9999;}
.forms-admin-popup__backdrop{position:absolute;inset:0;background:rgba(15,23,42,.54);backdrop-filter:blur(6px);}
.forms-admin-popup__dialog{position:relative;display:flex;align-items:center;justify-content:center;min-height:100%;padding:24px;}
.forms-admin-popup__card{position:relative;width:min(920px,100%);max-height:calc(100vh - 48px);overflow:auto;padding:22px;border:1px solid rgba(255,255,255,.72);border-radius:24px;background:linear-gradient(180deg,rgba(255,255,255,.98),rgba(244,247,255,.94));box-shadow:0 28px 60px rgba(15,23,42,.24);}
.forms-admin-popup__close{position:absolute;top:18px;right:18px;z-index:3;display:inline-flex;align-items:center;justify-content:center;width:46px;height:46px;padding:0;border:0;border-radius:14px;background:rgba(83,107,152,.12);color:#43516b;font-size:32px;font-weight:400;line-height:1;box-shadow:0 10px 18px rgba(15,23,42,.08);transition:background .18s ease,color .18s ease,transform .18s ease;}
.forms-admin-popup__close:hover{background:rgba(83,107,152,.22);color:#1f2b42;transform:translateY(-1px);}
.forms-submission-popup{display:grid;gap:18px}
.forms-submission-popup__header{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;padding-right:54px}
.forms-submission-popup__eyebrow{margin:0 0 8px;color:var(--sz-muted);font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
.forms-submission-popup__title{margin:0;color:var(--sz-text);font-size:clamp(28px,3vw,40px);font-weight:800;line-height:1.05;letter-spacing:-.04em}
.forms-submission-popup__meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.forms-submission-popup__meta div{padding:14px 16px;border:1px solid var(--sz-line);border-radius:16px;background:rgba(255,255,255,.72)}
.forms-submission-popup__meta span{display:block;margin-bottom:6px;color:var(--sz-muted);font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
.forms-submission-popup__meta strong{display:block;color:var(--sz-text);font-size:14px;line-height:1.55;word-break:break-word}
.forms-submission-popup__section{display:grid;gap:10px}
.forms-submission-popup__section h4{margin:0;color:var(--sz-text);font-size:20px;font-weight:800}
.forms-submission-popup__table{margin:0;overflow:hidden;border:1px solid var(--sz-line);border-radius:18px;background:rgba(255,255,255,.74)}
.forms-submission-popup__table th{width:240px;color:var(--sz-muted);font-size:13px;font-weight:800;background:rgba(247,249,252,.82)}
.forms-submission-popup__table td{color:var(--sz-text);font-size:15px;line-height:1.6}
.forms-submission-popup__actions{display:flex;gap:10px;flex-wrap:wrap}
@media (max-width: 767px){.forms-admin-popup__dialog{padding:14px}.forms-admin-popup__card{padding:18px}.forms-submission-popup__meta{grid-template-columns:1fr}.forms-submission-popup__table th,.forms-submission-popup__table td{display:block;width:100%}.forms-submission-popup__table th{border-bottom:0;padding-bottom:8px}.forms-submission-popup__table td{padding-top:0}}
CSS, [], 'forms-submission-popup-css');

$this->registerJs(<<<JS
(() => {
  if (window.__formsSubmissionPopupInit) return;
  window.__formsSubmissionPopupInit = true;

  const popup = document.createElement('div');
  popup.className = 'forms-admin-popup';
  popup.hidden = true;
  popup.innerHTML =
    '<div class="forms-admin-popup__backdrop" data-forms-popup-close></div>' +
    '<div class="forms-admin-popup__dialog">' +
      '<div class="forms-admin-popup__card">' +
        '<button type="button" class="forms-admin-popup__close" data-forms-popup-close aria-label="Закрыть">&times;</button>' +
        '<div class="forms-admin-popup__content"></div>' +
      '</div>' +
    '</div>';
  document.body.appendChild(popup);

  const content = popup.querySelector('.forms-admin-popup__content');

  function closePopup() {
    popup.hidden = true;
    content.innerHTML = '';
    document.body.classList.remove('forms-modal-body-lock');
  }

  function openPopup(html) {
    content.innerHTML = html;
    popup.hidden = false;
    document.body.classList.add('forms-modal-body-lock');
  }

  document.addEventListener('click', async (event) => {
    const trigger = event.target.closest('[data-submission-popup]');
    if (trigger) {
      event.preventDefault();
      const url = trigger.getAttribute('href');
      if (!url) return;
      content.innerHTML = '<div class="forms-submission-popup"><strong>Загрузка...</strong></div>';
      popup.hidden = false;
      document.body.classList.add('forms-modal-body-lock');
      const response = await fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
      const html = await response.text();
      openPopup(html);
      return;
    }

    if (event.target.closest('[data-forms-popup-close]')) {
      event.preventDefault();
      closePopup();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !popup.hidden) {
      closePopup();
    }
  });
})();
JS, View::POS_END, 'forms-submission-popup-js');
?>
<div class="sz-panel">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'form_id', 'value' => fn($m) => $m->form?->title ?: $m->form?->name],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => static function ($m) {
                    $label = \larikmc\forms\models\Submission::statuses()[$m->status] ?? $m->status;
                    $class = match ($m->status) {
                        \larikmc\forms\models\Submission::STATUS_NEW => 'text-bg-info',
                        \larikmc\forms\models\Submission::STATUS_VIEWED => 'text-bg-secondary',
                        \larikmc\forms\models\Submission::STATUS_PROCESSED => 'text-bg-success',
                        \larikmc\forms\models\Submission::STATUS_SPAM => 'text-bg-danger',
                        default => 'text-bg-light',
                    };
                    return Html::tag('span', Html::encode($label), ['class' => 'badge ' . $class]);
                },
            ],
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [
                    'view' => static function ($url, $model) {
                        return Html::a('<span class="material-symbols-rounded">visibility</span>', ['popup', 'id' => $model->id], [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Просмотр',
                            'title' => 'Просмотр',
                            'data-submission-popup' => '1',
                        ]);
                    },
                    'delete' => static function ($url) {
                        return Html::a('<span class="material-symbols-rounded">delete</span>', $url, [
                            'class' => 'sz-row-action',
                            'aria-label' => 'Удалить',
                            'title' => 'Удалить',
                            'data-method' => 'post',
                            'data-confirm' => 'Удалить эту заявку?',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>
