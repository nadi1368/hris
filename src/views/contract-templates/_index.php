<?php

use hesabro\hris\models\ContractTemplates;
use hesabro\hris\Module;

/** @var $model ContractTemplates */

?>

<div class="col-md-12 my-3 text-center" style="border-bottom: 1px solid rgba(0,0,0,0.2);">
	<h2><?= Module::t('module', 'Contract Clauses') ?></h2>
</div>

<table class="table table-hover">
	<thead>
		<tr>
			<th><?php echo $model->getAttributeLabel('title'); ?></th>
			<th><?php echo $model->getAttributeLabel('description'); ?></th>
		</tr>
	</thead>

	<tbody>
    <?php if (is_array($model->clausesModels) && count($model->clausesModels)):?>
		<?php foreach ($model->clausesModels as $template): ?>
			<tr>
				<td><?php echo $template->title; ?></td>
				<td><?php echo $template->description; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
    <?php endif; ?>
</table>
