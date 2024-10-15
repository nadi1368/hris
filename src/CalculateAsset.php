<?php

namespace hesabro\hris;

use yii\web\AssetBundle;

/**
 * Class PrintLetterAsset
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