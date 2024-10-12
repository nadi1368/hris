<?php

use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\Module;
use yii\widgets\Pjax;
use hesabro\helpers\widgets\TableView;

/* @var $this yii\web\View */
/* @var $salaryPeriod hesabro\hris\models\SalaryPeriod */
/* @var $searchModel hesabro\hris\models\SalaryPeriodItemsSearch */
/* @var $searchModelUser hesabro\hris\models\EmployeeBranchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderUser yii\data\ActiveDataProvider */

$this->title = $salaryPeriod->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Salary Periods'), 'url' => ['salary-period/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'p-jax-salary-period-items', 'timeout' => false]); ?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $salaryPeriod,
            'attributes' => [
                'title',
                [
                    'attribute' => 'workshop_id',
                    'value' => function ($model) {
                        return $model->workshop->fullName;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'start_date',
                    'value' => function ($salaryPeriod) {
                        return Yii::$app->jdf->jdate("Y/m/d", $salaryPeriod->start_date);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'end_date',
                    'value' => function ($salaryPeriod) {
                        return Yii::$app->jdf->jdate("Y/m/d", $salaryPeriod->end_date);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($salaryPeriod) {
                        return SalaryPeriod::itemAlias('Status', $salaryPeriod->status);
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'قابل پرداخت',
                    'value' => function ($salaryPeriod) {
                        return number_format((float)$salaryPeriod->getSalaryPeriodItems()->sum(SalaryPeriodItems::getFinalPaymentStringAttributes()));
                    },
                    'format' => 'raw'
                ],
            ]
        ]); ?>
    </div>
    <div class="card-footer">
        <?= $this->render('_btn', [
            'salaryPeriod' => $salaryPeriod,
        ]) ?>
    </div>
</div>

<?= $this->render('_pre-document', [
    'salaryPeriod' => $salaryPeriod,
]) ?>
<div class="row">
    <div class="col-md-2">
        <?= $this->render('_list-employee-branch', [
            'salaryPeriod' => $salaryPeriod,
            'dataProviderUser' => $dataProviderUser,
            'searchModelUser' => $searchModelUser,
        ]) ?>
    </div>
    <div class="col-md-10">
        <?= $this->render('_list-salary-items', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]) ?>
    </div>
</div>
<?php Pjax::end(); ?>
<?= $this->render('_form-excel-bank', [
    'model' => $salaryPeriod,
]) ?>
