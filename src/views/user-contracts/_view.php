<?php



/* @var $this \yii\web\View */
/* @var $model UserContracts */

use hesabro\hris\models\ContractClausesModel;
use hesabro\hris\models\UserContracts;
use hesabro\hris\Module;
use yii\helpers\Html;

$css = <<< CSS
@page {
    size: A4 portrait;
    margin-bottom: 0.5cm;
    margin-top: 1cm;
}

.font-12 {
	font-size: 12px;
}


CSS;
$this->registerCss($css);

?>

<?= !$print ? Html::a('چاپ', ['view', 'id' => $model->id, 'print' => 1], ['class' => 'btn btn-info']) : '' ?>


<table>

	<thead>
	<tr>
		<th>
			<?= \yii\bootstrap4\Html::img(Module::getInstance()->settings::get('company_logo_for_contracts'), ['class' => 'mt-2 mb-2', 'style' => ['height' => '17.2mm', 'text-align' => 'right', 'display' => 'block']]); ?>
			<hr>
		</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td>
			<div class="row">
				<div class="col-lg-12 mb-4">
					<div class="card-header text-center">
						<h4><?= $model->contractTitle ?></h4>
					</div>
					<div class="card-body" style="background-color: rgba(0,0,0,0.03);">
						<?= UserContracts::changeVariables($model->contractDescription, $model->variables) ?>
					</div>
				</div>

				<?php foreach ($model->contractClauses as $clause):
					/** @var ContractClausesModel $clause */
					?>
					<div class="col-lg-12" style="text-align: justify;
  text-justify: inter-word;">
						<div class="card-header">
							<h4><?= $clause->title ?></h4>
						</div>
						<div class="card-body font-12">
							<?= nl2br(UserContracts::changeVariables($clause->description, $model->variables, true)) ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</td>
	</tr>
	<!-- Page content -->

	</tbody>

	<tfoot>
	<tr >
		<th>
			<div id="footer">
				<hr>
				<?= UserContracts::changeVariables($model->contractSignatures, $model->variables) ?>
			</div>
		</th>
	</tr>

	</tfoot>

</table>



