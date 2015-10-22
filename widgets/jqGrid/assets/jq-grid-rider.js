/**
 * Created by Андрей on 21.10.2015.
 */

var jqGridRider = {
    // Постобработка
    run: function($, jqGrid, jqGridOption, navParam, navEdit, navAdd, navDel, navSearch, navView) {
        navView.beforeShowForm = function(form) {
            jqGridRider.hideInView($, jqGridOption, form[0]);
        };
        navView.afterclickPgButtons = function(whichbutton, form, rowid) {
            jqGridRider.hideInView($, jqGridOption, form[0]);
        };

        navEdit.errorTextFormat = jqGridRider.errorTextFormat;
        navAdd.errorTextFormat = jqGridRider.errorTextFormat;
        navDel.errorTextFormat = jqGridRider.errorTextFormat;
        navSearch.errorTextFormat = jqGridRider.errorTextFormat;
        navView.errorTextFormat = jqGridRider.errorTextFormat;
    },
    // Скрывает скрытые поля из детального просмотра
    hideInView: function ($, jqGridOption, form) {
        for (var i in jqGridOption.colModel) {
            if (
                'hidden' in jqGridOption.colModel[i]
                && 'name' in jqGridOption.colModel[i]
                && jqGridOption.colModel[i].hidden
            ) {
                $('tr#trv_' + jqGridOption.colModel[i].name, form).hide();
            }
        }
    },
    // Форматирмирует сообщения об ошибках
    errorTextFormat: function (data) {
        if ('responseJSON' in data && 'message' in data.responseJSON) {
            return 'Error: ' + data.responseJSON.message;
        }
        return 'Error: See jq-grid-rider.js';
    },
    // Датапикер
    dateTimePicker: function (el) {
        $(el).datetimepicker({format: 'yyyy-mm-dd hh:ii:ss', minuteStep: 5});
    }
};