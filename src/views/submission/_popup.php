<?php
use larikmc\forms\helpers\SubmissionContextHelper;
use larikmc\forms\models\Submission;
use yii\helpers\Html;

$statusLabel = Submission::statuses()[$model->status] ?? $model->status;
$statusClass = match ($model->status) {
    Submission::STATUS_NEW => 'text-bg-info',
    Submission::STATUS_VIEWED => 'text-bg-secondary',
    Submission::STATUS_PROCESSED => 'text-bg-success',
    Submission::STATUS_SPAM => 'text-bg-danger',
    default => 'text-bg-light',
};
?>
<div class="forms-submission-popup">
    <div class="forms-submission-popup__header">
        <div>
            <p class="forms-submission-popup__eyebrow">Заявка</p>
            <h3 class="forms-submission-popup__title"><?= Html::encode($model->form?->name ?: $model->form?->title) ?></h3>
        </div>
        <span class="badge <?= Html::encode($statusClass) ?>"><?= Html::encode($statusLabel) ?></span>
    </div>

    <div class="forms-submission-popup__meta">
        <div><span>Дата</span><strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></strong></div>
        <div><span>Страница</span><strong><?= Html::encode($model->page_url ?: '—') ?></strong></div>
        <div><span>Referrer</span><strong><?= Html::encode($model->referrer ?: '—') ?></strong></div>
        <div><span>IP</span><strong><?= Html::encode($model->ip ?: '—') ?></strong></div>
    </div>

    <div class="forms-submission-popup__section">
        <h4>Данные формы</h4>
        <table class="table forms-submission-popup__table">
            <tbody>
            <?php foreach ($model->getData() as $key => $value): ?>
                <tr>
                    <th><?= Html::encode($key) ?></th>
                    <td><?= Html::encode((string) $value) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php $contextRows = SubmissionContextHelper::displayRows($model->getContext()); ?>
    <?php if ($contextRows): ?>
        <div class="forms-submission-popup__section">
            <h4>Контекст страницы и объекта</h4>
            <table class="table forms-submission-popup__table"><tbody>
            <?php foreach ($contextRows as $label => $value): ?>
                <tr><th><?= Html::encode($label) ?></th><td><?= Html::encode($value) ?></td></tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
    <?php endif; ?>

    <div class="forms-submission-popup__actions">
        <?= Html::a('Обработано', ['update-status', 'id' => $model->id, 'status' => Submission::STATUS_PROCESSED], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Спам', ['update-status', 'id' => $model->id, 'status' => Submission::STATUS_SPAM], ['class' => 'btn btn-warning']) ?>
    </div>
</div>
