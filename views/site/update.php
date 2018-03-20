<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Update';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-update">
	<h1><?= Html::encode($this->title) ?></h1>
	<div class="row">
		<div class="col-lg-12">
			<?php $form = ActiveForm::begin(['id' => 'form-update']); ?>

			<div>
				<h4>Personal Information</h4>
					<div class="row">
						<div class="col-lg-4">
							<?= $form->field($model, 'name') -> label('Name') -> textInput() ?>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4">
							<?= $form->field($model, 'email')-> input('email') ?>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-2">
							<?= $form->field($model, 'status')->dropDownList([
			                    'Activated' => 'Activated', 
			                    'Rejected' => 'Rejected'],
			                    ['prompt' => '---Select Status---'
			                    ]) ?>
						</div>
					</div>
			</div>

			<div>
				<h4>Bank Account Information</h4>
				<a><b>Account Number</a>
					<div class="row">
						<div class="col-lg-4">
							<?= $form->field($model, 'account_number') -> label(false) -> textInput() ?>
						</div>
						<div class="col-lg-3">
							<?= Html::submitButton('Random', ['name' => 'random', 'value' => 'random_1','class'=> 'btn btn-info']); ?>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4">
							<?= $form->field($model, 'current_balance') -> label('Current Balance') -> textInput(['readonly' => true]) ?>
						</div>
					</div>
				<?= Html::submitButton('Update', ['name' => 'update', 'value' => 'update_1', 'class'=> 'btn btn-primary']); ?>
	
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>