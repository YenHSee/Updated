<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'User Reset Password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'form-resetpassword']); ?>
			
			<div class="row">
				<div class="col-lg-3">
					<?= $form->field($model, 'password') -> label('Current Password') -> passwordInput() ?>
			    </div>
			</div>

			<div class= "row">
				<div class="col-lg-3">
					<?= $form->field($model, 'password') -> label('New Password') -> passwordInput() ?>
			    </div>
			</div>

			<div class="row">
				<div class="col-lg-3">
					<?= $form->field($model, 'password') -> label('Confirm Password') -> passwordInput() ?>
			    </div>
			</div>

			</div>

		<div class="row">
			<div class="col-lg-3">
				<div class="pull-right">
					<?= Html::submitButton('Submit', ['class'=> 'btn btn-primary']); ?>
				</div>
			</div>
		</div>

	<?php ActiveForm::end(); ?>
</div>
