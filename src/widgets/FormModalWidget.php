<?php
namespace larikmc\forms\widgets;

use larikmc\forms\assets\FormsModalAsset;
use larikmc\forms\helpers\TemplateHelper;
use larikmc\forms\models\Form;
use larikmc\forms\Module;
use yii\base\Widget;
use yii\web\View;

class FormModalWidget extends Widget
{
    public int $formId = 0;
    public string $buttonLabel = 'Оставить заявку';
    public ?string $formTemplate = null;
    public ?string $modalTemplate = null;
    public ?string $formView = null;
    public ?string $modalView = null;
    public array $buttonOptions = [];
    public array $modalOptions = [];
    public array $formOptions = [];

    public function run(): string
    {
        $module = \Yii::$app->getModule('forms');
        if (!$module instanceof Module) { return ''; }
        if ($this->formId <= 0) {
            return YII_DEBUG ? '<!-- Form id is required -->' : '';
        }
        $form = Form::find()->where(['is_active' => 1, 'id' => $this->formId])->one();
        if (!$form) {
            return YII_DEBUG ? "<!-- Form (id={$this->formId}) not found or inactive -->" : '';
        }

        $view = $this->getView();
        FormsModalAsset::register($view);
        $this->registerClientCode($view);
        $uid = $this->getId() . '-form-' . $form->id . '-' . substr(md5(uniqid('', true)), 0, 8);
        $modalId = 'forms-modal-' . $form->id . '-' . substr(md5(uniqid('', true)), 0, 8);
        $formHtml = FormWidget::widget([
            'formId' => $form->id,
            'template' => $this->formTemplate,
            'view' => $this->formView,
            'formOptions' => $this->formOptions,
            'showHeading' => false,
            'showDescription' => true,
        ]);

        return $this->render(TemplateHelper::resolve('modal', $this->modalTemplate, $this->modalView, $module), compact('form','uid','modalId','formHtml') + [
            'buttonLabel'=>$this->buttonLabel,'buttonOptions'=>$this->buttonOptions,'modalOptions'=>$this->modalOptions,'widget'=>$this,
        ]);
    }

    private function registerClientCode(View $view): void
    {
        $view->registerCss(<<<CSS
.forms-modal-body-lock{overflow:hidden;}
.forms-widget__alert{margin:0 0 16px;padding:14px 16px;border-radius:16px;font-size:15px;line-height:1.55;font-weight:700;}
.forms-widget__alert--success{color:#175337;border:1px solid rgba(61,188,126,.28);background:linear-gradient(135deg,rgba(61,188,126,.16),rgba(30,167,110,.08));}
.forms-widget__alert--error{color:#6c2330;border:1px solid rgba(224,81,93,.25);background:linear-gradient(135deg,rgba(224,81,93,.16),rgba(176,47,66,.08));}
.forms-modal{position:fixed;inset:0;z-index:2147483000;display:none;}
.forms-modal--open{display:block;}
.forms-modal__backdrop{position:absolute;inset:0;background:rgba(15,23,42,.54);backdrop-filter:blur(6px);}
.forms-modal__dialog{position:relative;display:flex;align-items:center;justify-content:center;min-height:100%;padding:24px;}
.forms-modal__card{position:relative;width:min(680px,100%);max-height:calc(100vh - 48px);overflow:auto;border:1px solid rgba(83,107,152,.18);border-radius:24px;background:linear-gradient(180deg,rgba(255,255,255,.98),rgba(244,247,255,.94));box-shadow:0 28px 60px rgba(15,23,42,.24);}
.forms-modal__header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:24px 42px 0;}
.forms-modal__title{margin:0;color:#182033;font-size:28px;font-weight:800;line-height:1.08;letter-spacing:-.04em;}
.forms-modal__close{position:relative;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;width:44px;height:44px;padding:0;border:0;border-radius:14px;background:rgba(83,107,152,.08);transition:background .18s ease;}
.forms-modal__close:hover{background:rgba(83,107,152,.16);}
.forms-modal__close-icon{position:relative;display:block;width:18px;height:18px;}
.forms-modal__close-icon::before,.forms-modal__close-icon::after{content:"";position:absolute;top:50%;left:50%;width:18px;height:2px;border-radius:999px;background:#4f5f7b;transform-origin:center;}
.forms-modal__close-icon::before{transform:translate(-50%,-50%) rotate(45deg);}
.forms-modal__close-icon::after{transform:translate(-50%,-50%) rotate(-45deg);}
.forms-modal__body{padding:24px 42px 34px;}
@media (max-width: 767px){.forms-modal__dialog{padding:14px;}.forms-modal__card{max-height:calc(100vh - 28px);border-radius:20px;}.forms-modal__header{padding:16px 16px 0;}.forms-modal__body{padding:14px 16px 16px;}.forms-modal__title{font-size:22px;}}
CSS, [], 'forms-modal-inline-css');

        $view->registerJs(<<<JS
(() => {
  if (window.__formsModalInit) return;
  window.__formsModalInit = true;
  const activeClass = 'forms-modal--open';
  const bodyClass = 'forms-modal-body-lock';

  function getModal(trigger) {
    const selector = trigger.getAttribute('data-forms-modal-open');
    return selector ? document.querySelector(selector) : null;
  }

  function ensureModalInBody(modal) {
    if (!modal || modal.parentElement === document.body) return;
    document.body.appendChild(modal);
  }

  function openModal(modal) {
    if (!modal) return;
    ensureModalInBody(modal);
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

  document.querySelectorAll('.forms-modal').forEach((modal) => {
    ensureModalInBody(modal);
    if (modal.querySelector('.forms-widget__alert')) {
      openModal(modal);
    }
  });
})();
JS, View::POS_END, 'forms-modal-inline-js');
    }
}
