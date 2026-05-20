# larikmc/yii2-forms

Yii2 extension for building forms in admin panel and rendering them on frontend.

The extension is designed for projects that use `larikmc/yii2-admin` and need:

- a forms section in admin panel
- a global fields directory
- form assembly from existing fields
- inline form output on page
- popup form output by button click
- form template overrides inside the project
- submission storage in admin panel

## Features

- one form entity, different render modes
- global reusable fields
- form-specific field settings
- own popup implementation without frontend framework dependency
- own frontend form CSS and JavaScript
- client-side validation without `jquery` and `yii.activeForm`
- email notifications with module-level and per-form recipients
- support for multiple forms and multiple popups on one page
- file-based PHP templates
- safe submission storage in JSON
- server-side validation
- honeypot protection

## Requirements

- PHP `>= 8.1`
- Yii2 `~2.0`
- `yiisoft/yii2-bootstrap5`
- `larikmc/yii2-admin`

## Installation

```bash
composer require larikmc/yii2-forms
```

## Configuration

Register the module in `backend` and `frontend`.

Example:

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
        'notificationEmails' => 'sales@example.com, lead@example.com',
    ],
],
```

## Migrations

```bash
php yii migrate --migrationPath=@vendor/larikmc/yii2-forms/src/migrations
```

The extension creates tables:

- `forms_form`
- `forms_field`
- `forms_form_field`
- `forms_submission`

## Admin Menu Integration

Example menu config for `larikmc/yii2-admin`:

```php
[
    'icon' => 'fact_check',
    'label' => 'Формы',
    'items' => [
        [
            'label' => 'Формы',
            'url' => ['/forms/form/index'],
        ],
        [
            'label' => 'Поля форм',
            'url' => ['/forms/field/index'],
        ],
        [
            'label' => 'Заявки',
            'url' => ['/forms/submission/index'],
        ],
    ],
],
```

## Data Model

### Form

Stores frontend form settings:

- form title
- description
- submit button text
- success message
- form-specific notification emails
- active status

`slug` is generated automatically.

`name` is synchronized from `title` internally and is not intended for manual editing.

### Field

Global field directory.

Example fields:

- Имя
- Телефон
- Email
- Комментарий

Default field label is always taken from field name.

### FormField

Stores field settings inside a specific form:

- sort order
- required flag
- placeholder override
- hint override
- active flag

### Submission

Stores sent form data in JSON.

## Field Types

Available field types:

- `text`
- `textarea`
- `phone`
- `email`
- `number`
- `select`
- `checkbox`
- `radio`
- `hidden`

For `phone`, the extension uses its own frontend mask and validation logic without jQuery or Yii ActiveForm JS.

Default phone mask:

```text
+7 (999) 999-99-99
```

For `select`, `checkbox`, `radio`, values are read from `options_json`.

Example:

```json
[
  { "value": "one", "label": "Первый вариант" },
  { "value": "two", "label": "Второй вариант" }
]
```

## Admin Workflow

Minimal scenario:

1. Create fields in `/forms/field/index`
2. Create a form in `/forms/form/index`
3. Open form editing
4. Go to tab `Поля`
5. Add fields to form
6. Open tab `Код вставки`
7. Copy widget code
8. Insert widget into frontend view
9. Receive submissions in `/forms/submission/index`

## Inline Form

Render form directly on page:

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'order',
]) ?>
```

Frontend widget behavior:

- loads its own CSS and JS automatically
- does not require `bootstrap.css`
- does not require `jquery.js`
- does not require `yii.js`
- does not require `yii.validation.js`
- does not require `yii.activeForm.js`

With template:

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'order',
    'template' => 'compact',
]) ?>
```

With direct view alias:

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'order',
    'view' => '@app/views/custom/order-form.php',
]) ?>
```

## Popup Form

Render button and open form in extension popup:

```php
<?= \larikmc\forms\widgets\FormModalWidget::widget([
    'slug' => 'order',
    'buttonLabel' => 'Оставить заявку',
]) ?>
```

Popup behavior:

- custom modal implementation from the extension
- no Bootstrap modal JavaScript dependency
- supports multiple popups on one page
- success message can be shown directly inside popup after submit

With templates:

```php
<?= \larikmc\forms\widgets\FormModalWidget::widget([
    'slug' => 'order',
    'buttonLabel' => 'Оставить заявку',
    'formTemplate' => 'compact',
    'modalTemplate' => 'default',
]) ?>
```

With direct view aliases:

```php
<?= \larikmc\forms\widgets\FormModalWidget::widget([
    'slug' => 'order',
    'buttonLabel' => 'Оставить заявку',
    'formView' => '@app/views/forms/custom/form.php',
    'modalView' => '@app/views/forms/custom/modal.php',
]) ?>
```

## Widget Options

### FormWidget

```php
public string $slug;
public ?string $template = null;
public ?string $view = null;
public array $options = [];
public array $formOptions = [];
public bool $ajax = false;
```

### FormModalWidget

```php
public string $slug;
public string $buttonLabel = 'Оставить заявку';
public ?string $formTemplate = null;
public ?string $modalTemplate = null;
public ?string $formView = null;
public ?string $modalView = null;
public array $buttonOptions = [];
public array $modalOptions = [];
public array $formOptions = [];
```

## Templates

The extension searches templates in this order:

1. direct view alias from widget config
2. project custom templates path
3. bundled extension templates

Default bundled templates:

- `src/views/widgets/form/default.php`
- `src/views/widgets/modal/default.php`
- `src/views/widgets/fields/default.php`

Project overrides example:

- `@app/views/forms/templates/form/default.php`
- `@app/views/forms/templates/form/compact.php`
- `@app/views/forms/templates/modal/default.php`
- `@app/views/forms/templates/modal/dark.php`

## Copying Templates

Copy form template:

From:

```text
vendor/larikmc/yii2-forms/src/views/widgets/form/default.php
```

To:

```text
@app/views/forms/templates/form/compact.php
```

Then use:

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'order',
    'template' => 'compact',
]) ?>
```

Copy modal template:

From:

```text
vendor/larikmc/yii2-forms/src/views/widgets/modal/default.php
```

To:

```text
@app/views/forms/templates/modal/my-modal.php
```

Then use:

```php
<?= \larikmc\forms\widgets\FormModalWidget::widget([
    'slug' => 'order',
    'buttonLabel' => 'Оставить заявку',
    'modalTemplate' => 'my-modal',
]) ?>
```

## Multiple Forms On One Page

Supported scenarios:

- several inline forms
- several popup forms
- several buttons for same form
- different templates on one page

The extension generates unique IDs for:

- form instances
- popup containers
- open buttons

This prevents DOM and JS conflicts.

## Submissions

Route:

```text
/forms/submit/index
```

What is stored:

- form id
- status
- submitted data JSON
- page URL
- referrer
- IP
- user-agent
- created date

## Email Notifications

The extension can send e-mail notifications after form submit.

Global module setting:

```php
'modules' => [
    'forms' => [
        'class' => \larikmc\forms\Module::class,
        'notificationEmails' => 'sales@example.com, lead@example.com',
    ],
],
```

You can pass:

- one e-mail as string
- several e-mails separated by comma
- array of e-mails

Per-form override:

- open form editing in admin
- fill field `E-mail для уведомлений`
- if this field is filled, the form will use its own addresses
- if this field is empty, the form will use module-level addresses

If no notification e-mails are configured, the form is still submitted and stored normally.

## Consent Checkbox

By default, frontend forms include a required consent checkbox before submit:

- user confirms personal data processing consent
- checkbox is validated on client and server
- checkbox value is not stored in submission JSON

The default text is:

```text
Даю согласие на обработку персональных данных для обработки моего обращения и обратной связи со мной. Ознакомлен(а) с Политикой обработки персональных данных.
```

Statuses:

- `new`
- `viewed`
- `processed`
- `spam`

## Security

The extension includes:

- Yii CSRF protection
- honeypot field
- required consent checkbox
- server-side validation
- client-side validation in extension JavaScript
- safe JSON decode
- HTML encoding in admin views

## Current Notes

- popup frontend is implemented inside the extension and does not depend on Bootstrap modal JS
- frontend form rendering uses extension CSS and JS instead of Yii ActiveForm assets
- phone mask and validation are implemented in extension JavaScript
- default field label is always field name
- form title is the main user-facing form name
- submissions are always stored

## Example

Inline:

```php
<?= \larikmc\forms\widgets\FormWidget::widget([
    'slug' => 'consultation',
]) ?>
```

Popup:

```php
<?= \larikmc\forms\widgets\FormModalWidget::widget([
    'slug' => 'consultation',
    'buttonLabel' => 'Оставить заявку',
]) ?>
```
