<?php namespace app\widgets\jqGrid;
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 22.10.2015
 * Time: 13:01
 */

use yii\web\ErrorHandler;
use yii\web\Response;

class JqErrorHandler extends ErrorHandler {

    /**
     * Renders the exception.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        if (!($exception instanceof JqException)) {
            parent::renderException($exception);
            return;
        }
        if (\Yii::$app->has('response')) {
            $response = \Yii::$app->getResponse();
        } else {
            $response = new Response();
        }
        $response->setStatusCode(500);
        $response->data['message'] = $exception->getMessage();
        $response->send();
    }
}