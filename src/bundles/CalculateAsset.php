<?php

namespace hesabro\hris\bundles;

use yii\web\AssetBundle;

/**
 * Class CalculateAsset
 * @package hesabro\hris
 * @author Nader <nader.bahadorii@gmail.com>
 */
class CalculateAsset extends AssetBundle
{
    public $sourcePath = '@hesabro/hris/assets';

    public $css = [
    ];
    public $js = [
        'js/salary-calculate.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}