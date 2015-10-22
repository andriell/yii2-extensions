<?php namespace app\widgets\jqGrid;
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 22.10.2015
 * Time: 13:04
 */

use yii\base\Exception;
use yii\base\Model;

class JqException extends Exception {
    /**
     * @param Model $model
     * @throws JqException
     */
    public static function throwModel($model) {
        $errors = $model->getErrors();
        if (empty($errors)) {
            return;
        }
        $message = '';
        foreach ($errors as $name => $error) {
            if (!is_array($error)) {
                continue;
            }
            $message .= $name . ': ';
            foreach ($error as $e) {
                $message .= $e . '; ';
            }
        }
        throw new self($message);
    }
}