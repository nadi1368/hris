<?php

namespace hesabro\hris;

use yii\web\AssetBundle;

class ProfileUpdateAssets extends AssetBundle
{
    public $sourcePath = '@hesabro/hris/assets';

    public $js = [
        'js/photoviewer.min.js',
        'js/profile-update.js',
    ];

    public $css = [
        'css/photoviewer.min.css',
        'css/profile-update.css'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}