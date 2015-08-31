<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

$fullTableName = $generator->generateTableName($tableName);

echo '<?php namespace ' . $generator->ns . ";\n";
?>
/**
 * This is the model class for table "<?= $fullTableName ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */

use Yii;

class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
<?php
    foreach ($tableSchema->columns as $column):
        $const = \yii\helpers\Inflector::id2camel($column->name, '_');
?>
    const c<?= $const ?> = '<?= $column->name ?>';
    const f<?= $const ?> = '<?= $fullTableName . '.' . $column->name ?>';
<?php endforeach; ?>

<?php foreach ($relations as $name => $relation): ?>
    const r<?= $name ?> = '<?= lcfirst($name) ?>';
<?php endforeach; ?>

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $fullTableName ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>
<?php
// Преобразуем литералы в константы
$rules = preg_replace_callback('#^\s*\[(\[[^\]]+\])#si', function($m) {
    $columns = [];
    $r = '[[';
    eval('$columns = ' . $m[1] . ';');
    $prefix = '';
    foreach($columns as $name) {
        $r .= $prefix . 'self::c' . \yii\helpers\Inflector::id2camel($name, '_');
        $prefix = ', ';
    }
    $r .= ']';
    return $r;
}, $rules);
?>
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            self::c<?= \yii\helpers\Inflector::id2camel($name, '_') ?> => <?= $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
