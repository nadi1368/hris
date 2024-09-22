<?php

use hesabro\hris\models\Letter;
use common\models\Settings;
use yii\bootstrap4\Html;

/**
 * @var Letter $letter
 * @var bool $with_header
 */

$variables = $letter->getVariablesValue();
$with_header=true;

$position = Settings::get('position_indicator_number_print') ?: '0,0';
$position = explode(',', str_replace('،', ',', $position));
$x = $position[0];
$y = $position[1];
$y2 = $position[2] ?? 20;
?>

<table <?= $with_header ? 'style="margin-top:'. Settings::get('top_margin_factor_print').'cm; width:100%;' : 'style="width: 100%;"'?>>
    <thead>
    <?php if (!$with_header): ?>
        <tr>
            <th class="text-right">
                <?= Html::img(Settings::get('company_logo_for_contracts'), ['class' => 'mt-2 mb-2', 'style' => ['height' => '17.2mm', 'text-align' => 'right', 'display' => 'block']]); ?>
            </th>
            <th class="text-right" style="width: 33%">
                <p style="position: absolute; top: <?= $x ?>; left: <?= $y ?>; margin-block-start: 0 !important; margin-block-end: 0 !important; font-weight: normal;">
                    <?= Yii::t('app', 'Letter Number') ?>:
                    <?= $letter->employeeRequest?->indicator?->document_number ?: '' ?>
                </p>
                <p style="position: absolute; top: <?= $x + $y2 ?>; left: <?= $y ?>; margin-block-start: 0 !important; margin-block-end: 0 !important; font-weight: normal;">
                    <?= Yii::t('app', 'Date') ?>:
                    <?= $letter->employeeRequest?->indicator?->date ? Yii::$app->jdf->jdate("Y/m/d",$letter->employeeRequest?->indicator?->date): '' ?>
                </p>
            </th>
        </tr>
    <?php else : ?>
        <tr>
            <th class="text-right" style="width: 33%">
                <p style="position: absolute; top: <?= $x ?>; left: <?= $y ?>; margin-block-start: 0 !important; margin-block-end: 0 !important; font-weight: normal;">
                    <?= $letter->employeeRequest?->indicator?->document_number ?: '' ?>
                </p>
                <p style="position: absolute; top: <?= $x + $y2 ?>; left: <?= $y ?>; margin-block-start: 0 !important; margin-block-end: 0 !important; font-weight: normal;">
                    <?= $letter->employeeRequest?->indicator?->date ? Yii::$app->jdf->jdate("Y/m/d",$letter->employeeRequest?->indicator?->date): '' ?>
                </p>
            </th>
        </tr>
    <?php endif; ?>
    </thead>

    <tbody>
    <tr>
        <td colspan="2">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card-body" style="background-color: rgba(0,0,0,0.03);">
                        <?= strtr($letter->contractTemplate->description, $variables) ?>
                    </div>
                </div>
                <?php if (is_array($letter->contractTemplate->clauses) && count($letter->contractTemplate->clauses)): ?>
                    <?php foreach ($letter->contractTemplate->clauses as $clause): ?>
                        <div class="col-lg-12" style="text-align: justify; text-justify: inter-word; font-size: 12px">
                            <?= strtr($clause['description'], $variables) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <!-- Page content -->
    </tbody>

    <tfoot>
    <tr>
        <th colspan="2">
            <div id="footer">
                <?= strtr($letter->contractTemplate->signatures, $variables) ?>
            </div>
        </th>
    </tr>
    </tfoot>
</table>
