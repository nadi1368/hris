<?php

use common\models\PayMethodOnline;
use yii\helpers\Html;
use api\modules\company\models\IPG;

/**
 * @var $this \yii\web\View
 * @var $rows array
 * @var $platform integer
 */

$this->title = "در حال ارسال به نرم افزار ابرسا...";

$js = <<<JS
console.log('sending...');
function sendExcelBankToNative(fileName,rows) {
    var variables = {}; // my object
    var objects = {}; // my object
    objects = {
        rowsDate: rows
    };
   variables = {
        fileName: fileName,
    };
   
   textStatus = sendToNativeBankCsv(variables, 'melat', objects);
   if (textStatus == 'error') {
        return false;
    }

    if (textStatus == 'error') {
        showtoast("خطا در ارسال پرینت", 'error');
    } else {
        showtoast("پرینت ارسال شد.", 'success');
    }
}
sendExcelBankToNative('{$fileName}','{$rows}');
JS;
$this->registerJs($js);

?>
<div class="payment-view">
    <div class="text-center">

        <h1 class="m-4"><?= Html::encode($this->title) ?></h1>
        <i class="fas fa-spinner fa-pulse fa-3x text-success"></i>
    </div>
</div>
