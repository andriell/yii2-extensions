<?php namespace app\widgets\jqGrid;

/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 19.10.2015
 * Time: 17:05
 */

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class JqGridWidget extends Widget
{
    public $idGrid;
    public $idPager;
    public $jQuery;

    public $grid = [];
    public $navParam = [];
    public $navEdit = [];
    public $navAdd = [];
    public $navDel = [];
    public $navSearch = [];
    public $navView = [];

    public $gridDefault = [
        'width' => 'auto',
        'height' => 'auto',
        'rowNum' => 20,
    ];
    public $navParamDefault = [
        'edit' => false,
        'add' => false,
        'del' => false,
        'search' => false,
        'refresh' => true,
        'view' => true,
    ];
    public $navEditDefault = [];
    public $navAddDefault = [];
    public $navDelDefault = [];
    public $navSearchDefault = [
        'multipleSearch' => true,
        'multipleGroup' => true,
    ];
    public $navViewDefault = [];

    // Фильтры над колонками
    public $filterToolbar = true;

    public function init()
    {
        parent::init();
        if (empty($this->idGrid)) {
            $this->idGrid = 'jqGrid-' . $this->id;
        }
        if (empty($this->idPager)) {
            $this->idPager = 'jqGridNav-' . $this->id;
        }
        if (empty($this->jQuery)) {
            $this->jQuery = 'jQuery';
        }
    }

    public function run()
    {
        $r = [];
        $r['gridOption'] = ArrayHelper::merge($this->gridDefault, $this->grid);
        $r['gridOption']['pager'] = '#' . $this->idPager;

        $r['navParam'] = ArrayHelper::merge($this->navParamDefault, $this->navParam);
        $r['navEdit'] = ArrayHelper::merge($this->navEditDefault, $this->navEdit);
        $r['navAdd'] = ArrayHelper::merge($this->navAddDefault, $this->navAdd);
        $r['navDel'] = ArrayHelper::merge($this->navDelDefault, $this->navDel);
        $r['navSearch'] = ArrayHelper::merge($this->navSearchDefault, $this->navSearch);
        $r['navView'] = ArrayHelper::merge($this->navViewDefault, $this->navView);
        return $this->renderFile(__DIR__ . '/views/jqGrid.php', $r);
    }

    /**
     * @param $name
     * @param bool $default
     * @return bool
     */
    public function getNavParam($name, $default = false) {
        return isset($this->navParam[$name]) ? $this->navParam[$name] : $default;
    }
}