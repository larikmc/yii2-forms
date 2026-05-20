<?php
namespace larikmc\forms\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

class Submission extends ActiveRecord
{
    public const STATUS_NEW='new'; public const STATUS_VIEWED='viewed'; public const STATUS_PROCESSED='processed'; public const STATUS_SPAM='spam';
    public static function tableName(): string { return '{{%forms_submission}}'; }
    public function behaviors(): array { return [['class' => TimestampBehavior::class, 'createdAtAttribute' => 'created_at', 'updatedAtAttribute' => false, 'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']]]]; }
    public static function statuses(): array { return [self::STATUS_NEW=>'Новая',self::STATUS_VIEWED=>'Просмотрена',self::STATUS_PROCESSED=>'Обработана',self::STATUS_SPAM=>'Спам']; }
    public function rules(): array { return [[['form_id','data_json'],'required'],[['form_id','created_at','viewed_at'],'integer'],[['data_json'],'string'],[['status'],'string','max'=>32], [['status'],'in','range'=>array_keys(self::statuses())], [['page_url','referrer','ip','user_agent'],'string','max'=>1024]]; }
    public function attributeLabels(): array { return ['form_id'=>'Форма','status'=>'Статус','data_json'=>'Данные заявки','page_url'=>'Страница отправки','referrer'=>'Referrer','ip'=>'IP','user_agent'=>'User-Agent','created_at'=>'Дата создания','viewed_at'=>'Дата просмотра']; }
    public function getForm() { return $this->hasOne(Form::class,['id'=>'form_id']); }
    public function getData(): array { $d=json_decode((string)$this->data_json,true); return is_array($d)?$d:[]; }
}
