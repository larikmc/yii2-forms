<?php use yii\helpers\Html; \larikmc\forms\assets\FormsAdminAsset::register($this); ?>
<?= $this->render('_tabs', ['model' => $model, 'active' => 'code']) ?>
<div class="sz-panel">
    <div class="forms-code-banner">
        <div class="forms-code-banner__main">
            <h2 class="forms-code-banner__title">Подключение формы без ручной верстки</h2>
            <p class="forms-code-banner__text">Одна и та же форма может выводиться сразу на странице или открываться в popup. Ниже готовые варианты, которые можно просто вставить в нужный шаблон.</p>
        </div>
    </div>

    <div class="sz-ui-kit-grid forms-ui-grid">
        <section class="sz-panel sz-ui-kit-panel">
            <p class="sz-ui-kit-section-label">Inline</p>
            <h3 class="sz-ui-kit-section-title">Встроенная форма</h3>
            <p class="sz-ui-kit-subtitle">Форма показывается сразу на странице и подходит для лендингов, карточек услуг и блоков контактов.</p>
            <pre id="code1" class="sz-ui-kit-code"><code><?= Html::encode("<?= \\larikmc\\forms\\widgets\\FormWidget::widget(['formId' => {$model->id}]) ?>") ?></code></pre>
            <div class="sz-ui-kit-actions">
                <button class="btn btn-primary forms-copy-btn" data-forms-copy="#code1">Скопировать код</button>
            </div>
        </section>

        <section class="sz-panel sz-ui-kit-panel">
            <p class="sz-ui-kit-section-label">Template</p>
            <h3 class="sz-ui-kit-section-title">Встроенная форма с шаблоном</h3>
            <p class="sz-ui-kit-subtitle">Используется выбранный шаблон оформления. Удобно, когда на сайте несколько визуальных зон с разным стилем.</p>
            <pre id="code2" class="sz-ui-kit-code"><code><?= Html::encode("<?= \\larikmc\\forms\\widgets\\FormWidget::widget(['formId' => {$model->id}, 'template' => 'default']) ?>") ?></code></pre>
            <div class="sz-ui-kit-actions">
                <button class="btn btn-primary forms-copy-btn" data-forms-copy="#code2">Скопировать код</button>
            </div>
        </section>

        <section class="sz-panel sz-ui-kit-panel">
            <p class="sz-ui-kit-section-label">Popup</p>
            <h3 class="sz-ui-kit-section-title">Кнопка + popup</h3>
            <p class="sz-ui-kit-subtitle">На странице видна только кнопка. По клику форма открывается в модальном окне без перехода на другую страницу.</p>
            <pre id="code3" class="sz-ui-kit-code"><code><?= Html::encode("<?= \\larikmc\\forms\\widgets\\FormModalWidget::widget(['formId' => {$model->id}, 'buttonLabel' => 'Оставить заявку']) ?>") ?></code></pre>
            <div class="sz-ui-kit-actions">
                <button class="btn btn-primary forms-copy-btn" data-forms-copy="#code3">Скопировать код</button>
            </div>
        </section>

        <section class="sz-panel sz-ui-kit-panel">
            <p class="sz-ui-kit-section-label">Mix</p>
            <h3 class="sz-ui-kit-section-title">Popup с шаблонами</h3>
            <p class="sz-ui-kit-subtitle">Отдельные шаблоны можно задать и для самой формы, и для модального окна. Это самый гибкий вариант.</p>
            <pre id="code4" class="sz-ui-kit-code"><code><?= Html::encode("<?= \\larikmc\\forms\\widgets\\FormModalWidget::widget(['formId' => {$model->id}, 'buttonLabel' => 'Оставить заявку', 'formTemplate' => 'default', 'modalTemplate' => 'default']) ?>") ?></code></pre>
            <div class="sz-ui-kit-actions">
                <button class="btn btn-primary forms-copy-btn" data-forms-copy="#code4">Скопировать код</button>
            </div>
        </section>
    </div>
</div>
