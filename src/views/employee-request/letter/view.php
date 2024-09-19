<?php
use hesabro\hris\models\EmployeeRequest;
use yii\web\View;

/**
 * @var EmployeeRequest $employeeRequest
 * @var bool $print
 * @var View $this
 * @var ?string $content
 */

$css = <<< CSS
@page {
    margin-bottom: 0.5cm;
    margin-top: 1cm;
}
.font-12 {
	font-size: 12px;
}
CSS;
$this->registerCss($css);
?>

<?php if (!$content): ?>
<div class="flex align-items-center justify-content-center">
    <?= Yii::t('app', 'Not results found.') ?>
</div>
<?php else: ?>
    <?= $content ?>
<?php endif; ?>
