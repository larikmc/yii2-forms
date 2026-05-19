<?php
namespace larikmc\forms\controllers;

use larikmc\forms\models\Form;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class FormController extends AdminController
{
    public function actionIndex(){ $dataProvider=new ActiveDataProvider(['query'=>Form::find()->orderBy(['id'=>SORT_DESC])]); return $this->render('index',compact('dataProvider')); }
    public function actionCreate(){ $model=new Form(['is_active'=>1,'store_submissions'=>1,'submit_label'=>'Отправить']); if($model->load(\Yii::$app->request->post())&&$model->save()){ return $this->redirect(['update','id'=>$model->id]); } return $this->render('create',compact('model')); }
    public function actionUpdate(int $id){ $model=$this->findModel($id); if($model->load(\Yii::$app->request->post())&&$model->save()){\Yii::$app->session->setFlash('success','Сохранено');} return $this->render('update',compact('model')); }
    public function actionDelete(int $id){ $this->findModel($id)->delete(); return $this->redirect(['index']); }
    public function actionFields(int $id){ $model=$this->findModel($id); return $this->render('fields',compact('model')); }
    public function actionCode(int $id){ $model=$this->findModel($id); return $this->render('code',compact('model')); }
    public function actionSubmissions(int $id){ $model=$this->findModel($id); return $this->render('submissions',compact('model')); }
    protected function findModel(int $id): Form { $m=Form::findOne($id); if(!$m){throw new NotFoundHttpException();} return $m; }
}
