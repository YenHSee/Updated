<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Testing';
$this->params['breadcrumbs'][] = $this->title;
?>

<div>

	<h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(['id' => 'form-duringTransfer']); ?>

		<?= Html::submitButton('Submit 1', ['name' => 'submit1', 'value' => 'submit_1']) ?>
		<?= Html::submitButton('Submit 2', ['name' => 'submit2', 'value' => 'submit_2']) ?>

	<?php ActiveForm::end(); ?>

</div>