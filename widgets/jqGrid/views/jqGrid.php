<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 19.10.2015
 * Time: 17:11
 */

use app\widgets\jqGrid\JqJson;

/** @var $this \yii\web\View */
/** @var $gridOption array */
/** @var $navParam array */
/** @var $navEdit array */
/** @var $navAdd array */
/** @var $navDel array */
/** @var $navSearch array */
/** @var $navView array */

\app\widgets\jqGrid\JqGridAsset::register($this);

/** @var \app\widgets\jqGrid\JqGridWidget $context */
$context = $this->context;
?>

<div>
    <table id="<?php echo $context->idGrid ?>"></table>
    <div id="<?php echo $context->idPager ?>"></div>
</div>
<script type="text/javascript">
    (function ($) {
        $(document).ready(function () {
            var jqGrid = $('#<?php echo $context->idGrid ?>');
            var jqGridOption = <?= JqJson::encodeJs($gridOption) ?>;
            var navParam = <?= JqJson::encodeJs($navParam) ?>;
            var navEdit = <?= JqJson::encodeJs($navEdit) ?>;
            var navAdd = <?= JqJson::encodeJs($navAdd) ?>;
            var navDel = <?= JqJson::encodeJs($navDel) ?>;
            var navSearch = <?= JqJson::encodeJs($navSearch) ?>;
            var navView = <?= JqJson::encodeJs($navView) ?>;

            jqGridRider.run($, jqGrid, jqGridOption, navParam, navEdit, navAdd, navDel, navSearch, navView);

            jqGrid.jqGrid(jqGridOption);
            // add navigation bar with some built in actions for the grid
            jqGrid.navGrid('#<?php echo $context->idPager ?>', navParam, navEdit, navAdd, navDel, navSearch, navView);
            <?php if ($context->filterToolbar) : ?>
            jqGrid.jqGrid('filterToolbar', <?= JqJson::encodeJs($context->filterToolbar) ?>);
            <?php endif; ?>
            <?php if ($context->gridResize) : ?>
            jqGrid.jqGrid('gridResize', <?= JqJson::encodeJs($context->gridResize) ?>);
            <?php endif; ?>
        });
    })(<?php echo $context->jQuery ?>);

</script>
