# larikmc/yii2-forms

Yii2 forms builder extension with admin integration.

## Installation

```bash
composer require larikmc/yii2-forms
```

## Configuration

```php
'modules' => [
    'forms' => [
        'class' => \larikmc\forms\Module::class,
        'adminPermission' => 'admin',
        'storeClientInfo' => true,
        'defaultFormTemplate' => 'default',
        'defaultModalTemplate' => 'default',
        'customTemplatesPath' => '@app/views/forms/templates',
        'submitRoute' => ['/forms/submit/index'],
        'defaultSuccessRedirect' => null,
    ],
],
```

## Migrations

```bash
php yii migrate --migrationPath=@vendor/larikmc/yii2-forms/src/migrations
```

## Admin menu integration

```php
[
    'icon' => 'fact_check',
    'label' => 'Формы',
    'items' => [
        ['label' => 'Формы', 'url' => ['/forms/form/index']],
        ['label' => 'Поля форм', 'url' => ['/forms/field/index']],
        ['label' => 'Заявки', 'url' => ['/forms/submission/index']],
    ],
],
```

## Using inline form

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'order',
]) ?>
```

## Using modal form

```php
<?= \larikmc\forms\widgets\FormModalWidget::widget([
    'slug' => 'order',
    'buttonLabel' => 'Оставить заявку',
]) ?>
```

## Using templates

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'order',
    'template' => 'compact',
]) ?>
```

```php
<?= \larikmc\forms\widgets\FormModalWidget::widget([
    'slug' => 'order',
    'buttonLabel' => 'Оставить заявку',
    'formTemplate' => 'compact',
    'modalTemplate' => 'dark',
]) ?>
```

## Copying templates

Copy:

- `vendor/larikmc/yii2-forms/src/views/widgets/form/default.php`

To:

- `@app/views/forms/templates/form/my-template.php`

Then:

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'order',
    'template' => 'my-template',
]) ?>
```

Copy modal template from:

- `vendor/larikmc/yii2-forms/src/views/widgets/modal/default.php`

To:

- `@app/views/forms/templates/modal/my-modal.php`

## Field types

`text`, `textarea`, `phone`, `email`, `number`, `select`, `checkbox`, `radio`, `hidden`

## Submissions

Saved in `forms_submission.data_json`.
