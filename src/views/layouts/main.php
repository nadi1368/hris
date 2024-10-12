<?php
/* @var $this \yii\web\View */

/* @var $content string */

use hesabro\hris\assets\MainAssets;
use common\components\jdf\Jdf;
use common\models\Settings;
use common\widgets\Alert;
use common\widgets\dateRangePicker\RangePickerAsset;
use common\widgets\OnboardingChecklistWidget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

RangePickerAsset::register($this);
MainAssets::register($this);

?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html dir="rtl" lang="<?= Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="theme-color" content="#2874f0">
		<meta http-equiv="content-language" content="fa"/>
		<?= Html::csrfMetaTags() ?>
		<link rel="shortcut icon" href="<?= Yii::getAlias('@web') . "/img/hesabro.png"; ?>" type="image/png"/>
        <title><?= Html::encode(($this->title ?: '') . ' ' . Module::t('module', 'Admin') . ' ' . Module::t('module', 'HR')) . ' (HRIS)' ?></title>
		<?php $this->head() ?>
	</head>
	<body>

	<?php $this->beginBody(); ?>
	<div class="preloader">
		<div class="lds-ripple">
			<div class="lds-pos"></div>
			<div class="lds-pos"></div>
		</div>
	</div>
	<div id="main-wrapper">
		<?= $this->render('_header') ?>
		<?= $this->render('_sidebar') ?>
		<div class="page-wrapper">
			<div class="page-breadcrumb bg-white">
				<div class="row">
					<?php if (isset($this->params['breadcrumbs'])): ?>
						<div class="col-12 align-self-start align-self-md-center">
							<nav class="mt-2">
								<?= Breadcrumbs::widget([
									'options' => ['class' => 'breadcrumb mb-0 justify-content-start  p-0 bg-white'],
									'homeLink' => [
										'label' => '<span class="fal fa-home"></span>',
										'url' => \common\components\Url::home(),
										'encode' => false// Requested feature
									],
									'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
									'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
									'activeItemTemplate' => "<li class=\"breadcrumb-item active\">{link}</li>\n",
								]) ?>
							</nav>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="page-content container-fluid text-left">
				<div>
					<?= Alert::widget() ?>
				</div>
				<?= $content ?>

				<?php
				Modal::begin([
					'headerOptions' => ['id' => 'modalPjaxHeader'],
					'id' => 'modal-pjax',
					'bodyOptions' => [
						'id' => 'modalPjaxContent',
						'class' => 'p-3',
						'data' => ['show-preloader' => 0]
					],
					'options' => ['tabindex' => false]
				]); ?>
				<div class="text-center">
					<div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
						<span class="sr-only">Loading...</span>
					</div>
				</div>
				<?php Modal::end(); ?>

				<?php
				Modal::begin([
					'headerOptions' => ['id' => 'modalHeader'],
					'id' => 'modal',
					//keeps from closing modal with esc key or by clicking out of the modal.
					// user must click cancel or X to close
					'clientOptions' => [],
					'options' => ['tabindex' => false]
				]);
				echo "<div id='modalContent'></div>";
				Modal::end();
				?>

                <?php
                Modal::begin([
                    'headerOptions' => ['id' => 'modalPjaxOverHeader'],
                    'id' => 'modal-pjax-over',
                    'bodyOptions' => [
                        'id' => 'modalPjaxOverContent',
                        'class' => 'p-3',
                        'data' => ['show-preloader' => 0]
                    ],
                    'options' => ['tabindex' => false, 'style' => 'z-index:1051;']
                ]); ?>
                <div class="text-center">
                    <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <?php Modal::end(); ?>

			</div>

		</div>

		<?= OnboardingChecklistWidget::widget() ?>
	</div>
	<?php $this->endBody() ?>
    <?= $this->render('@backend/views/layouts/_footer') ?>
	</body>
	</html>
<?php
$today_date = Yii::$app->jdf->jdate('Y/m/d');

$startAndEndOfCurrentMonth = Jdf::getStartAndEndOfCurrentMonth();
$first_month = Yii::$app->jdf->jdate('Y/m/d', $startAndEndOfCurrentMonth[0]);
$last_month = Yii::$app->jdf->jdate('Y/m/d', $startAndEndOfCurrentMonth[1]);

$startAndEndOfPreMonth = Jdf::getStartAndEndOfPreMonth();
$pre_first_month = Yii::$app->jdf->jdate('Y/m/d', $startAndEndOfPreMonth[0]);
$pre_last_month = Yii::$app->jdf->jdate('Y/m/d', $startAndEndOfPreMonth[1]);

$main_domain = YII_DEBUG ? "http://localhost/crm/managerCrm/" : Settings::get("web_site_domain");
$script_doday_value = <<< JS
today_date="$today_date";
first_month="$first_month";
last_month="$last_month";
pre_first_month="$pre_first_month";
pre_last_month="$pre_last_month";
main_domain="$main_domain";
JS;
$this->registerJs($script_doday_value);

$this->endPage();


?>