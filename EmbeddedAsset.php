<?php

namespace achertovsky\bluesnap;

use yii\web\AssetBundle;
use achertovsky\bluesnap\helpers\EmbeddedCheckout;
use common\overrides\helpers\ArrayHelper;

class EmbeddedAsset extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public $sourcePath = '@achertovsky/bluesnap/assets';

    /**
     * @inheritDoc
     */
    public $js = [
        'embedded.js',
    ];
    
    /**
     * Dynamic set of bluesnap javascript dependency
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->js[] = EmbeddedCheckout::getHost().'/web-sdk/4/bluesnap.js';
    }

    /**
     * @inheritDoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
