<?php namespace app\widgets\jqGrid;
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 20.10.2015
 * Time: 21:19
 */

use yii\base\Security;
use \yii\helpers\Json;

class JqJson extends Json {
    private static $q;

    /**
     * @return mixed
     */
    private static function getQ()
    {
        if (empty(self::$q)) {
            $s = new Security();
            self::$q = '!#@' . $s->generateRandomString(5) . '@#!';
        }
        return self::$q;
    }


    public static function encodeJs($value, $options = 320)
    {
        $q = self::getQ();
        $r = parent::encode($value, $options);
        $r = preg_replace('/[\'"]?' . $q . '[\'"]?/', '', $r);
        return $r;
    }

    public static function addJs($js) {
        $q = self::getQ();
        return $q . $js . $q;
    }
}