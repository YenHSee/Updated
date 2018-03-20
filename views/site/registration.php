<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'User Register';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'form-register']); ?>
		<div><h4>User Information</h4>
			<div class="row">
				<div class="col-lg-12">
					<?= $form->field($model, 'username') -> label('Username') -> textInput() ?>
			    	<?= $form->field($model, 'password')-> label('Password') -> passwordInput() ?>
			    	<?= $form->field($model, 'confirm_password')-> label('Confirm Password') -> passwordInput() ?>
			    	<!-- <?= $form->field($model, 'role') -> label('Username') -> textInput() ?> -->
			    	<?= $form->field($model, 'name') -> label('Name') -> textInput() ?>
			    	<?= $form->field($model, 'email') -> label('Email') -> input('email') ?>
			    	<?= $form->field($model, 'security_question')->dropDownList([
                    'Are you dog?' => 'Are you dog?', 
                    'Are you cat?' => 'Are you cat?'],
                    ['prompt' => '---Select Data---'
                    ]) ?>
                    <?= $form->field($model, 'security_answer') -> label('Security Answer') -> textInput() ?>
			    </div>
			</div>


		<div class="row">
			<div class="col-lg-6">
				<div class="pull-right">
					<?= Html::submitButton('Submit', ['name' => 'submit', 'value' => 'submit', 'class'=> 'btn btn-primary']); ?>
				</div>
			</div>
		</div>

	<?php ActiveForm::end(); ?>
</div>
