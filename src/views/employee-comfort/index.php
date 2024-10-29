<?php

use hesabro\helpers\widgets\ListView;
use hesabro\hris\models\ComfortSearch;
use hesabro\hris\Module;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $searchModel ComfortSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Comforts List');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Profile'), 'url' => ['/profile/index']];
$this->params['breadcrumbs'][] = $this->title;

$searchBox = $this->render('_search', ['model' => $searchModel]);
$layout = <<<HTML
<div class='col-md-12 col-sm-12 text-left mb-3'>
    <div class='card-header d-flex align-items-center justify-content-start'>
        <a class="accordion-toggle collapsed" data-toggle="collapse" href="#collapseOne" aria-expanded="false">
            <i class="far fa-search"></i> جستجو
        </a>
    </div>
    <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
    $searchBox
    </div>
</div>
\n{items}\n
<div class='col-md-12 col-sm-12 col-md-8'>
    <div class='d-flex align-items-center justify-content-end' style='gap: 4px'>
        {pager}
    </div>
</div>
HTML;
Pjax::begin(['id' => 'p-jax-comfort', 'timeout' => false, 'enablePushState' => false]);
?>
<div class="row">
    <div class="col-12">
        <?= ListView::widget(
            [
                'dataProvider' => $dataProvider,
                'showOnEmpty' => true,
                'options' => [
                    'tag' => 'div',
                    'class' => 'row',
                    'id' => 'list-wrapper',
                ],
                'itemOptions' => [
                    'class' => 'col-xlg-3 col-xl-3 col-lg-3 col-md-3 col-sm-12 py-3',
                ],
                'emptyTextOptions' => [
                    'class' => 'col-12 d-flex align-items-center justify-content-center',
                    'style' => 'height: 50vh;'
                ],
                'layout' => $layout,
                'itemView' => '_list',
                'pager' => [
                    'options' => [
                        'class' => 'pagination',
                    ],
                    'linkOptions' => ['class' => 'page-link'],
                    'pageCssClass' => 'page-item',
                    'activePageCssClass' => 'active',
                    'disabledPageCssClass' => 'hide',
                ],
            ]) ?>
    </div>
</div>
<?php Pjax::end(); ?>
