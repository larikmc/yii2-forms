<?php
use larikmc\forms\assets\FormsAdminAsset;
use larikmc\forms\models\Submission;
use yii\helpers\Html;

FormsAdminAsset::register($this);

$statusLabel = Submission::statuses()[$model->status] ?? $model->status;
$statusClass = match ($model->status) {
    Submission::STATUS_NEW => 'text-bg-info',
    Submission::STATUS_VIEWED => 'text-bg-secondary',
    Submission::STATUS_PROCESSED => 'text-bg-success',
    Submission::STATUS_SPAM => 'text-bg-danger',
    default => 'text-bg-light',
};
?>

<div class="sz-panel forms-submission-card">
    <div class="forms-submission-card__top">
        <div>
            <p class="forms-submission-card__eyebrow">Заявка</p>
            <h2 class="forms-submission-card__title"><?= Html::encode($model->form?->title ?: $model->form?->name) ?></h2>
        </div>
        <span class="badge <?= Html::encode($statusClass) ?>"><?= Html::encode($statusLabel) ?></span>
    </div>

    <div class="forms-submission-card__info">
        <div class="forms-submission-card__info-item">
            <span>Дата</span>
            <strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></strong>
        </div>
        <div class="forms-submission-card__info-item">
            <span>Страница</span>
            <strong><?= Html::encode($model->page_url ?: '—') ?></strong>
        </div>
        <div class="forms-submission-card__info-item">
            <span>Referrer</span>
            <strong><?= Html::encode($model->referrer ?: '—') ?></strong>
        </div>
        <div class="forms-submission-card__info-item">
            <span>IP</span>
            <strong><?= Html::encode($model->ip ?: '—') ?></strong>
        </div>
    </div>

    <div class="forms-submission-card__block">
        <h3 class="forms-submission-card__subtitle">Данные формы</h3>
        <table class="table forms-submission-card__table">
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

    <div class="forms-submission-card__actions">
        <?= Html::a('Отметить как просмотренную', ['update-status', 'id' => $model->id, 'status' => Submission::STATUS_VIEWED], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Отметить как обработанную', ['update-status', 'id' => $model->id, 'status' => Submission::STATUS_PROCESSED], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отметить как спам', ['update-status', 'id' => $model->id, 'status' => Submission::STATUS_SPAM], ['class' => 'btn btn-warning']) ?>
    </div>
</div>
