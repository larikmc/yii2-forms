<?php
namespace larikmc\forms\controllers;
use larikmc\forms\models\Submission;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
class SubmissionController extends AdminController
{
public function actionIndex(){ $dataProvider=new ActiveDataProvider(['query'=>Submission::find()->with('form')->orderBy(['id'=>SORT_DESC])]); return $this->render('index',compact('dataProvider')); }
public function actionView(int $id){ $model=$this->findModel($id); if($model->status===Submission::STATUS_NEW){$model->status=Submission::STATUS_VIEWED;$model->viewed_at=time();$model->save(false);} return $this->render('view',compact('model')); }
public function actionPopup(int $id){ $model=$this->findModel($id); if($model->status===Submission::STATUS_NEW){$model->status=Submission::STATUS_VIEWED;$model->viewed_at=time();$model->save(false);} \Yii::$app->response->format=Response::FORMAT_HTML; return $this->renderPartial('_popup',['model'=>$model]); }
public function actionUpdateStatus(int $id,string $status){ $m=$this->findModel($id); if(isset(Submission::statuses()[$status])){$m->status=$status;$m->save(false);} return $this->redirect(['view','id'=>$id]); }
public function actionDelete(int $id){ $this->findModel($id)->delete(); return $this->redirect(['index']); }
protected function findModel(int $id): Submission { $m=Submission::findOne($id); if(!$m) throw new NotFoundHttpException(); return $m; }
}
