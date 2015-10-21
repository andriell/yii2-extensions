<?php namespace app\widgets\jqGrid;
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 20.10.2015
 * Time: 15:42
 */

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

abstract class JqTableAbstract extends JqGridWidget
{
    const EVENT_ADD_BEFORE_SAVE = 'EVENT_ADD_BEFORE_SAVE';
    const EVENT_EDIT_BEFORE_SAVE = 'EVENT_EDIT_BEFORE_SAVE';
    const EVENT_FOR_EACH_ROW = 'EVENT_FOR_EACH_ROW';

    /**
     * @return ActiveRecord
     */
    abstract public function getModel();

    /**
     * @return ActiveQuery
     */
    abstract public function getQuery();

    /**
     * @return ColModel
     */
    abstract public function getColModel();

    /**
     * Метод конфигурирования различных свойств. Вызываеться перед инициализацией
     * @return void
     */
    abstract public function config();

    /**
     * @param array $get
     * @param array $post
     * @return array|\yii\db\ActiveRecord[]
     */
    public function action($get, $post)
    {
        $action = isset($post['oper']) ? $post['oper'] : 'search';
        if ($action == 'del') {
            return $this->actionDel($get, $post);
        } elseif ($action == 'edit') {
            return $this->actionEdit($get, $post);
        } elseif ($action == 'add') {
            return $this->actionAdd($get, $post);
        }
        return $this->actionList($get, $post);
    }

    public function actionList($get, $post)
    {
        $get['_search'] = (bool) isset($get['_search']) ? $get['_search'] : false;
        $get['filters'] = isset($get['filters']) ? Json::decode($get['filters'], true) : [];
        $get['page'] = (int) isset($get['page']) ? $get['page'] : 1;
        $get['rows'] = (int) isset($get['rows']) ? $get['rows'] : 20;

        $get['sidx'] = isset($get['sidx']) ? $get['sidx'] : false;
        $get['sord'] = isset($get['sord']) ? $get['sord'] : 'asc';


        $query = $this->getQuery();
        $colModel = $this->getColModel();

        $r = [];
        $r['page'] = $get['page'];
        $r['records'] = $query->count();
        $r['total'] = (int) ceil($r['records'] / $get['rows']);
        $r['rows'] = [];

        $query->limit($get['rows'])->offset(($r['page'] - 1) * $get['rows']);

        if ($get['sidx'] && $colModel->canOrder($get['sidx'])) {
            if ($get['sord'] == 'desc') {
                $query->orderBy([$get['sidx'] => SORT_DESC]);
            } else {
                $query->orderBy([$get['sidx'] => SORT_ASC]);
            }
        }

        //<editor-fold desc="Фильтруем если надо">
        if ($get['_search'] && $this->getNavParam('search', false) && $get['filters']) {
            $where = $this->actionSearch($get['filters']);
            $query->andWhere($where);
        }
        //</editor-fold>

        $query->asArray();
        $all = $query->all();
        $names = $colModel->getNames();
        foreach ($all as $row) {
            $event = new EventForEachRow();
            $event->row = [];
            foreach ($names as $name) {
                if (!$colModel->canList($name)) {
                    continue;
                }
                $event->row[$name] = isset($row[$name]) ? $row[$name] : $colModel->getAttr($name, Ccm::cDefval, '');
            }
            $this->trigger(self::EVENT_FOR_EACH_ROW, $event);
            $r['rows'][] = $event->row;
        }
        return $r;
    }

    /**
     * @param array $filters
     * @return array
     */
    private function actionSearch($filters)
    {
        $colModel = $this->getColModel();
        $groupOp = 'AND';
        if (isset($filters['groupOp']) && $filters['groupOp'] == 'OR') {
            $groupOp = 'OR';
        }
        $where = [];
        foreach ($filters['rules'] as $rules) {
            if (!$colModel->canSearch($rules['field'])) {
                continue;
            }
            $where = [$groupOp, $where, $this->op($rules['op'], $rules['field'], $rules['data'])];
        }
        if (isset($filters['groups']) && is_array($filters['groups'])) {
            foreach ($filters['groups'] as $group) {
                $where = [$groupOp, $where, $this->actionSearch($group)];
            }
        }
        return $where;
    }

    private function op($op, $arg1, $arg2) {
        $op = strtolower($op);
        if ($op == 'eq') {
            return ['=', $arg1, $arg2];
        } elseif ($op == 'ne') {
            return ['<>', $arg1, $arg2];
        } elseif ($op == 'lt') {
            return ['<', $arg1, $arg2];
        } elseif ($op == 'le') {
            return ['<=', $arg1, $arg2];
        } elseif ($op == 'gt') {
            return ['>', $arg1, $arg2];
        } elseif ($op == 'ge') {
            return ['>=', $arg1, $arg2];
        } elseif ($op == 'bw') {
            return ['like', $arg1, str_replace('%', '', $arg2) . '%', false];
        } elseif ($op == 'bn') {
            return ['not like', $arg1, str_replace('%', '', $arg2) . '%', false];
        } elseif ($op == 'ew') {
            return ['like', $arg1, '%' . str_replace('%', '', $arg2), false];
        } elseif ($op == 'en') {
            return ['not like', $arg1, '%' . str_replace('%', '', $arg2), false];
        } elseif ($op == 'cn') {
            return ['like', $arg1, '%' . str_replace('%', '', $arg2) . '%', false];
        } elseif ($op == 'nc') {
            return ['not like', $arg1, '%' . str_replace('%', '', $arg2) . '%', false];
        } elseif ($op == 'nu') {
            return [$arg1 => null];
        } elseif ($op == 'nn') {
            return ['NOT', [$arg1 => null]];
        } elseif ($op == 'in') {
            return [$arg1 => preg_split('#;#', $arg2, null, PREG_SPLIT_NO_EMPTY)];
        } elseif ($op == 'ni') {
            return ['NOT', [$arg1 => preg_split('#;#', $arg2, null, PREG_SPLIT_NO_EMPTY)]];
        }
        return [];
    }

    /**
     * Получить первичный ключ для поиска из набора данных
     * @param array $post
     * @return BaseActiveRecord
     */
    private function findOneByPk($post) {
        $pk = $this->getColModel()->getPk($post);
        return $this->getModel()->findOne($pk);
    }

    /**
     * Установить атрибуты которые можно редактировать
     * @param BaseActiveRecord $model
     * @param array $post
     * @param bool $skipEmpty - не устанавливать пустые значения тоже
     * @return BaseActiveRecord
     */
    private function setEditableAttributes($model, $post, $skipEmpty = true) {
        $colModel = $this->getColModel();
        $names = $colModel->getNames();
        $attributes = $model->attributes();
        // Создает массив и заполняет его значениями, с определенными ключами
        $attributes = array_fill_keys($attributes, true);
        foreach ($names as $name) {
            if (!$colModel->canEdit($name)) {
                continue;
            }
            if (!(isset($post[$name]) && isset($attributes[$name]))) {
                continue;
            }
            if (empty($post[$name]) && $skipEmpty) {
                continue;
            }
            $model->$name = $post[$name];
        }
        return $model;
    }

    public function actionAdd($get, $post)
    {
        if (!$this->getNavParam('add', false)) {
            return [];
        }
        /** @var BaseActiveRecord $model */
        $model = $this->getModel();
        $model = $this->setEditableAttributes($model, $post, true);

        //<editor-fold desc="EVENT_ADD_BEFORE_SAVE">
        $event = new EventBeforeSave();
        $event->model = $model;
        $event->get = $get;
        $event->post = $post;
        $this->trigger(self::EVENT_ADD_BEFORE_SAVE, $event);
        //</editor-fold>

        if ($model->save()) {
            return [];
        }
        return $model->getErrors();
    }

    public function actionEdit($get, $post)
    {
        if (!$this->getNavParam('edit', false)) {
            return [];
        }
        /** @var BaseActiveRecord $model */
        $model = $this->findOneByPk($post);
        if (! ($model instanceof BaseActiveRecord)) {
            return [];
        }
        $model = $this->setEditableAttributes($model, $post, false);

        //<editor-fold desc="EVENT_EDIT_BEFORE_SAVE">
        $event = new EventBeforeSave();
        $event->model = $model;
        $event->get = $get;
        $event->post = $post;
        $this->trigger(self::EVENT_EDIT_BEFORE_SAVE, $event);
        //</editor-fold>

        if ($model->save()) {
            return [];
        }
        return $model->getErrors();
    }

    public function actionDel($get, $post)
    {
        if (!$this->getNavParam('del', false)) {
            return [];
        }

        $one = $this->findOneByPk($post);
        if ($one instanceof BaseActiveRecord) {
            $one->delete();
        }
        return [];
    }

    public function init()
    {
        $this->config();
        parent::init();
    }

    public function run()
    {
        $this->grid['colModel'] = $this->getColModel()->toArray();
        return parent::run();
    }
}
