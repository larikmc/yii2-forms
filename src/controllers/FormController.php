<?php
namespace larikmc\forms\controllers;

use larikmc\forms\models\Field;
use larikmc\forms\models\Form;
use larikmc\forms\models\FormField;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class FormController extends AdminController
{
    public function actionIndex(){ $dataProvider=new ActiveDataProvider(['query'=>Form::find()->orderBy(['id'=>SORT_DESC])]); return $this->render('index',compact('dataProvider')); }
    public function actionCreate(){ $model=new Form(['is_active'=>1,'store_submissions'=>1,'submit_label'=>'Отправить']); if($model->load(\Yii::$app->request->post())&&$model->save()){ return $this->redirect(['update','id'=>$model->id]); } return $this->render('create',compact('model')); }
    public function actionUpdate(int $id){ $model=$this->findModel($id); if($model->load(\Yii::$app->request->post())&&$model->save()){ \Yii::$app->session->setFlash('success','Форма сохранена'); return $this->redirect(['index']); } return $this->render('update',compact('model')); }
    public function actionDelete(int $id){ $this->findModel($id)->delete(); return $this->redirect(['index']); }
    public function actionFields(int $id){
        $model = $this->findModel($id);
        $linkModel = new FormField(['form_id' => $model->id, 'is_active' => 1, 'sort_order' => 100]);
        if ($linkModel->load(\Yii::$app->request->post()) && $linkModel->save()) {
            \Yii::$app->session->setFlash('success', 'Поле добавлено в форму');
            return $this->redirect(['fields', 'id' => $id]);
        }
        $availableFields = Field::find()->where(['is_active' => 1])->orderBy(['name' => SORT_ASC])->all();
        $dataProvider = new ActiveDataProvider(['query' => FormField::find()->where(['form_id' => $model->id])->with('field')->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]), 'pagination' => false]);
        return $this->render('fields', compact('model', 'linkModel', 'availableFields', 'dataProvider'));
    }
    public function actionUpdateField(int $id, int $formFieldId){
        $model = $this->findModel($id);
        $formField = FormField::findOne(['id' => $formFieldId, 'form_id' => $model->id]);
        if (!$formField) { throw new NotFoundHttpException(); }
        if ($formField->load(\Yii::$app->request->post()) && $formField->save()) {
            \Yii::$app->session->setFlash('success', 'Настройки поля сохранены');
        }
        return $this->redirect(['fields', 'id' => $id]);
    }
    public function actionDeleteField(int $id, int $formFieldId){
        $model = $this->findModel($id);
        $formField = FormField::findOne(['id' => $formFieldId, 'form_id' => $model->id]);
        if ($formField) { $formField->delete(); }
        \Yii::$app->session->setFlash('success', 'Поле удалено из формы');
        return $this->redirect(['fields', 'id' => $id]);
    }
    public function actionCode(int $id){ $model=$this->findModel($id); return $this->render('code',compact('model')); }
    public function actionSubmissions(int $id){ $model=$this->findModel($id); return $this->render('submissions',compact('model')); }
    protected function findModel(int $id): Form { $m=Form::findOne($id); if(!$m){throw new NotFoundHttpException();} return $m; }
}
