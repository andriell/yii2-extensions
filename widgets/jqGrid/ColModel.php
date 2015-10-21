<?php namespace app\widgets\jqGrid;

/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 21.10.2015
 * Time: 16:19
 */

use yii\helpers\ArrayHelper;

class ColModel implements \ArrayAccess
{
    private $data = array();
    private $default = [
        Ccm::cSortable => false,
        Ccm::cSearch => false,
        Ccm::cEditable => false,
    ];

    public function offsetSet($name, $value)
    {
        if (!is_string($name)) {
            throw new \Exception('It is not supported. Use the name as a key.');
        }
        if (isset($value['name'])) {
            unset($value['name']);
        }
        if (isset($this->data[$name])) {
            $this->data[$name] = ArrayHelper::merge($this->data[$name], $value);
        } else {
            $this->data[$name] = ArrayHelper::merge($this->default, $value);
        }
    }

    public function offsetExists($name)
    {
        return isset($this->data[$name]);
    }

    public function offsetUnset($name)
    {
        unset($this->data[$name]);
    }

    public function offsetGet($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Можно передавать в ответе на запрос
     * @param $name
     * @return bool
     */
    public function canList($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Можно сортировать по этой колонке
     * @param $name
     * @return bool
     */
    public function canOrder($name)
    {
        return $this->getAttr($name, Ccm::cSortable, false);
    }

    /**
     * Можно фильтровать по этой колонке
     * @param $name
     * @return bool
     */
    public function canSearch($name)
    {
        return $this->getAttr($name, Ccm::cSearch, false);
    }

    /**
     * Можно фильтровать по этой колонке
     * @param $name
     * @return bool
     */
    public function canEdit($name)
    {
        return $this->getAttr($name, Ccm::cEditable, false);
    }

    /**
     * Получить массив первичных ключей заполненных из массива значений
     * @param array $values
     * @param bool $default
     * @return array
     */
    public function getPk($values = [], $default = null)
    {
        $pk = [];
        foreach ($this->data as $name => $row) {
            if (isset($row[Ccm::cKey]) && $row[Ccm::cKey]) {
                $pk[$name] = isset($values[$name]) ? $values[$name] : $default;
            }
        }
        return $pk;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $r = [];
        foreach ($this->data as $name => $row) {
            $row['name'] = $name;
            $r[] = $row;
        }
        return $r;
    }

    /**
     * Получить массив имен
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->data);
    }

    /**
     * Получить значение атрибута
     * @param string $name
     * @param string $attrName
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getAttr($name, $attrName, $defaultValue = false)
    {
        return isset($this->data[$name]) && isset($this->data[$name][$attrName]) ? $this->data[$name][$attrName] : $defaultValue;
    }

    /**
     * Эта колонка доступна только в форме редактирования
     * @param array $name
     */
    public function onlyEditing($name)
    {
        $this->offsetSet($name, [
            Ccm::cEditrules => [
                'edithidden' => true,
            ],
            Ccm::cHidden => true,
            Ccm::cEditable => true,
        ]);
    }

    /**
     * Скрыть из таблицы, оставить в форме редактирования
     * @param string $name
     * @param array $value
     * @param bool $null
     * @throws \Exception
     */
    public function typeSelect($name, $value, $null = false)
    {
        $val = '';
        $prefix = '';
        foreach ($value as $k => $v) {
            $val .= $prefix . $k . ':' . $v;
            $prefix = ';';
        }
        $arr = [
            Ccm::cEditoptions => [
                'value' => $val,
            ],
            Ccm::cEdittype => 'select',
            Ccm::cStype => 'select',
        ];
        $arr[Ccm::cSearchoptions] = [
            'sopt' => ['eq', 'ne'],
            'value' => ':All;' . $val,
        ];
        if ($null) {
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nu';
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nn';
        }
        $this->offsetSet($name, $arr);
    }

    /**
     * Селект Y/N
     * @param string $name
     * @param bool $null
     */
    public function typeYN($name, $null = false)
    {
        $this->typeSelect($name, ['Y' => 'Yes', 'N' => 'No'], $null);
    }

    /**
     * Дата / время
     * @param string $name
     * @param bool $null
     * @throws \Exception
     */
    public function typeDateTime($name, $null = false)
    {
        $arr = [
            Ccm::cEditoptions => [
                'dataInit' => JqJson::addJs('jqGridRider.dateTimePicker'),
            ],
            Ccm::cEdittype => 'text',
            Ccm::cSearchoptions => [
                'sopt' => ['eq', 'ne', 'gt', 'ge', 'lt', 'le',],
                'dataInit' => JqJson::addJs('jqGridRider.dateTimePicker'),
            ],
        ];
        if ($null) {
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nu';
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nn';
        }
        $this->offsetSet($name, $arr);
    }

    /**
     * Пароль
     * @param $name
     */
    public function typePassword($name)
    {
        $this->offsetSet($name, [
            Ccm::cEdittype => 'password',
            Ccm::cFormatter => 'password',
        ]);
    }

    public function typeInteger($name, $null = false, $sopt = ['eq', 'ne', 'gt', 'ge', 'lt', 'le', 'in', 'ni'])
    {
        $arr = [
            Ccm::cEdittype => 'integer',
            Ccm::cSorttype => 'integer',
            Ccm::cSearchoptions => [
                'sopt' => $sopt,
            ],
        ];
        if ($null) {
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nu';
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nn';
        }
        $this->offsetSet($name, $arr);
    }

    public function typeText($name, $null = false, $sopt = ['eq', 'ne', 'bw', 'bn', 'ew', 'en', 'cn', 'nc', 'in', 'ni'])
    {
        $arr = [
            Ccm::cEdittype => 'text',
            Ccm::cSorttype => 'text',
            Ccm::cSearchoptions => [
                'sopt' => $sopt,
            ],
        ];
        if ($null) {
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nu';
            $arr[Ccm::cSearchoptions]['sopt'][] = 'nn';
        }
        $this->offsetSet($name, $arr);
    }

    public function typeLiteText($name, $null = false)
    {
        $this->typeText($name, $null, ['eq', 'ne', 'bw', 'bn', 'in', 'ni']);
    }
}