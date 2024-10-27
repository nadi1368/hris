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
class PrintAsset extends AssetBundle
{
    public $sourcePath = '@hesabro/hris/assets';

    public $css = [
        'fonts/iranSans/css/style.css',
        'fonts/bundle/css/style.css',
        'css/print.css',
    ];

    public $js = [];

    public $depends = [];
}