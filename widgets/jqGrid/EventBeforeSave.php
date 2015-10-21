<?php namespace app\widgets\jqGrid;
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 21.10.2015
 * Time: 1:52
 */

use yii\base\Event;

class EventBeforeSave extends Event {
    public $model;
    public $get;
    public $post;
}
