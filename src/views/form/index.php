<?php
use yii\grid\GridView; use yii\helpers\Html; ?>
<div class="sz-panel"><p><?= Html::a('Создать форму',['create'],['class'=>'btn btn-success']) ?></p><?= GridView::widget(['dataProvider'=>$dataProvider,'columns'=>['id','name','slug',['attribute'=>'is_active','format'=>'boolean'],['class'=>yii\grid\ActionColumn::class,'template'=>'{update} {delete} {fields} {code} {subs}','buttons'=>['fields'=>fn($u,$m)=>Html::a('Поля',['fields','id'=>$m->id]),'code'=>fn($u,$m)=>Html::a('Код',['code','id'=>$m->id]),'subs'=>fn($u,$m)=>Html::a('Заявки',['submissions','id'=>$m->id])]]]]) ?></div>
