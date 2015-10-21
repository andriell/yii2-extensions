<?php namespace app\widgets\jqGrid;

use yii\helpers\FileHelper;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JqGridAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/jqGrid/assets';
    public $css = [
        'jqGrid/css/ui.jqgrid.css',
        //'jqGrid/css/ui.jqgrid-bootstrap.css',
        'jqGrid/css/ui.jqgrid-bootstrap-ui.css',
        //'bootstrap/css/bootstrap.min.css',
        //'bootstrap/css/docs.css',
        //'bootstrap/css/font-awesome.min.css',
        //'bootstrap/css/font-awesome-ie7.min.css',
        //'bootstrap/css/jquery.ui.1.10.0.ie.css',
        'bootstrap/css/jquery-ui-1.10.0.custom.css',
        //'bootstrap/css/prettify.css',
        'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'
    ];
    public $js = [
        //'jQuery/js/jquery-1.11.0.min.js',
        'jq-grid-rider.js',
        'bootstrap/js/jquery-ui-1.10.0.custom.min.js',
        'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',
        'bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.ru.js',
        'jqGrid/js/i18n/grid.locale-ru.js',
        'jqGrid/js/jquery.jqGrid.min.js',

    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $jsOptions = array(
        'position' => View::POS_HEAD
    );

    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);
        // Копируем картинки
        $manager = $view->getAssetManager();
        $dst = $manager->getAssetUrl($this, 'bootstrap/css/images');
        $src = __DIR__ . '/assets/bootstrap/css/images';
        FileHelper::copyDirectory($src, $dst);
    }
}
