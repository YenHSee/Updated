<?php

/* @var $this yii\web\View */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
//use app\models\User;

$this->title = 'View History';
$this->params['breadcrumbs'][] = $this->title;

?>
            
<div class="site-viewhistory">
    <h1><?= Html::encode($this->title) ?></h1>	

<div class="row">
    
    <?php $form = ActiveForm::begin(['method' => 'get', 'layout' => 'inline']); ?>
        <div class="col-lg-3">
                <?= $form->field($searchModel, 'status')->textInput(['placeholder' => 'Please Enter Any Info']) ?>
        </div>
        <div class="col-lg-3">
                <?= Html::submitButton('Search', ['class' => 'btn btn-default']) ?>
        </div>    
    <?php ActiveForm::end(); ?>

</div>
    <?php
        echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'to_account',
            'from_account',
            'amount',
            'last_balance',
            'status',
            'details',
            'remark',
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'template' => '{update} {delete} {link} {urlCreator}',
            //     'buttons' => [
            //         'update' => function ($url,$model) {
            //             return Html::a(
            //                 '<span class="glyphicon glyphicon-pencil"></span>', 
            //                 Url::to('basic/web/site/update?id=' . $model->id, true)
            //                 );
            //         },
            //         'delete' => function ($url,$model) {
            //             //$url = Url::to(['site/SiteController', 'id' => $model->id]);
            //             return Html::a(
            //                 '<span class="glyphicon glyphicon-trash"></span>', 
            //                 Url::to('basic/web/site/delete?id=' . $model->id, true)
            //                 );
            //         },
            //         // 'link' => function ($url,$model) {
            //         //     return HtmL::a(
            //         //         '<span class="glyphicon glyphicon-link"></span>', 
            //         //         UrL::to('basic/web/site/view?id=' . $model->id, true)
            //         //         );
            //         ],
            //         'urlCreator' => function($action, $model, $key, $index) {
            //             // if ($action === 'update') {
            //             //     $url = 'site/update?id'.$model->id;
            //             //     return $url;
            //             // }
            //              return Html::a(
            //                 '<span class="glyphicon glyphicon-user"></span>', 
            //                 Url::to('site/viewwwww', true)
            //                 );
            //         }
            //     ],
            ],
        ]
    );

    ?>	
</div>

