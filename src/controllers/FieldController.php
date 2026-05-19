<?php
namespace larikmc\forms\controllers;
use larikmc\forms\models\Field;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
class FieldController extends AdminController
{
public function actionIndex(){ $dataProvider=new ActiveDataProvider(['query'=>Field::find()->orderBy(['id'=>SORT_DESC])]); return $this->render('index',compact('dataProvider')); }
public function actionCreate(){ $model=new Field(['is_active'=>1,'type'=>Field::TYPE_TEXT]); if($model->load(\Yii::$app->request->post())&&$model->save()){ return $this->redirect(['index']); } return $this->render('create',compact('model')); }
public function actionUpdate(int $id){ $model=$this->findModel($id); if($model->load(\Yii::$app->request->post())&&$model->save()){ return $this->redirect(['index']); } return $this->render('update',compact('model')); }
public function actionDelete(int $id){ $this->findModel($id)->delete(); return $this->redirect(['index']); }
protected function findModel(int $id): Field { $m=Field::findOne($id); if(!$m) throw new NotFoundHttpException(); return $m; }
}
