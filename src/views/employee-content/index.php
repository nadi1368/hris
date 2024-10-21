<?php

use hesabro\hris\models\EmployeeContentSearch;
use hesabro\helpers\components\iconify\Iconify;
use hesabro\hris\Module;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var EmployeeContentSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var int $faqId
 * @var string $clauseId
 * @var string $title
 */

$this->params['breadcrumbs'][] = $title;

$css = <<< CSS
.accordion .card-header {
    border-radius: 0 !important;
}

CSS;
$this->registerCss($css);

$this->registerCssFile('@web/fonts/bundle.css');
?>

<div class="card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <div class="card-body">

        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <?php if ($dataProvider->getCount() > 0) : ?>
                    <div id="accordion" class="accordion show collapse">
                        <?php foreach ($dataProvider->getModels() as $item) : ?>

                            <div class="mb-3 accordion-item card border shadow-sm">
                                <div id="<?= 'collapse-' . $item->id ?>-heading" class="card-header">
                                    <h5 class="mb-0 d-flex align-items-center justify-content-between">
                                        <a type="button" class="m-0 text-large collapsed" href="#<?= 'collapse-' . $item->id ?>" data-toggle="collapse" aria-expanded="false" aria-controls="<?= 'collapse-' . $item->id ?>">
                                            <span><?= $item->title ?></span>
                                        </a>
                                        <?= (
                                            $attachment = $item->getFileUrl('attachment')) ?
                                            Html::a(
                                                implode(' ', [
                                                    Html::tag('span', Iconify::getInstance()->icon('ph:download-simple'), ['class' => 'font-18']),
                                                    Module::t('module', 'Download'),
                                                    Module::t('module', 'File'),
                                                    Module::t('module', 'Attachment')
                                                ]),
                                                $attachment,
                                                ['target' => '_blank', 'download' => true, 'class' => 'd-flex align-items-center gap-1']) :
                                            ''
                                        ?>
                                    </h5>
                                </div>
                                <div id="<?= 'collapse-' . $item->id ?>" class="aa collapse <?= $faqId == $item->id || $item->containsClauseId($clauseId) ? 'show' : '' ?>" aria-labelledby="<?= 'collapse-' . $item->id ?>-heading" data-parent="#accordion">
                                    <div class="card-body">
                                        <?= $item->getContent($clauseId)  ?>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class='text-center py-5'><?= Module::t('module', 'No Data') ?></div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php

$script = <<<JS
    var highlightedClause = $('[id^=clause-].highlight')

    if(highlightedClause.lenth) {
        $('html, body').animate({
            scrollTop: highlightedClause.offset().top
        }, 1000);
    }
JS;

$this->registerJs($script);