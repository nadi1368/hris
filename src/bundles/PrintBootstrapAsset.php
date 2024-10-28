<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace hesabro\hris\bundles;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PrintBootstrapAsset extends AssetBundle
{
    public $sourcePath = '@hesabro/hris/assets';

    public $css = [
        'fonts/iranSansNumber/css/style.css',
        'css/bootstrap-4/bootstrap.min.css',
        'css/print-bootstrap.css',
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
