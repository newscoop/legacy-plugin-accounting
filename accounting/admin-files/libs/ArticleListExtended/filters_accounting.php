<?php
/**
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once $GLOBALS['g_campsiteDir'] . '/classes/Author.php';

$translator = \Zend_Registry::get('container')->getService('translator');

?>
<div class="filters">
<fieldset class="filters"><legend><?php echo $translator->trans('Filter', array(), 'library'); ?></legend>
    <div class="left">
        <dl>
            <dt><label for="filter_author"><?php echo $translator->trans('Author', array(), 'library'); ?></label></dt>
            <dd><input type="text" name="author" id="filter_author" class="select2 filters" data-contenturl="/admin/accounting/author/search.php" style="width:250px;" /></dd>
        </dl>
    </div>

    <div class="right">
        <div style="display:none">
            <input id="publish_date_from" type="text" name="publish_date_from" class="filters" />
            <input id="publish_date_to" type="text" name="publish_date_to" class="filters" />
        </div>
        <dl class="date-selection">
            <dt><label><?php echo $translator->trans('Date selection', array(), 'plugin_accounting'); ?></label></dt>
            <dd>
                <label>
                    <input type="radio" name="date_selection" value="monthly" class="date_selection" checked>
                    <?php echo $translator->trans('Per month', array(), 'plugin_accounting'); ?>
                </label>
                <label>
                    <input type="radio" name="date_selection" value="specific" class="date_selection">
                    <?php echo $translator->trans('Specific dates', array(), 'plugin_accounting'); ?>
                </label>
            </dd>
        </dl>

        <dl class="date-option date-show-monthly">
            <dt><label for="filter_date_monthly"><?php echo $translator->trans('Publish date', array(), 'plugin_accounting'); ?></label></dt>
            <dd>
                <input id="filter_date_monthly" type="text" name="filter_date_monthly" class="monthpicker notrigger filters" />
            </dd>
        </dl>

        <div class="date-option date-show-specific">
            <dl>
                <dt><label for="filter_date_specific_start"><?php echo $translator->trans('Publish date start', array(), 'plugin_accounting'); ?></label></dt>
                <dd><input id="filter_date_specific_start" type="text" name="filter_date_specific_start" class="datepicker notrigger filters" /></dd>
            </dl>
            <dl>
                <dt><label for="filter_date_specific_end"><?php echo $translator->trans('Publish date end', array(), 'plugin_accounting'); ?></label></dt>
                <dd><input id="filter_date_specific_end" type="text" name="filter_date_specific_end" class="datepicker notrigger filters" /></dd>
            </dl>
        </div>
    </div>
</fieldset>
</div>
<!-- /.smartlist-filters -->

		<?php if (!self::$renderFiltersAccounting) { ?>
<link rel="stylesheet" type="text/css" href="<?= $Campsite['SUBDIR'] ?>/plugins/accounting/js/select2/select2.css" />
<link type="text/css" href="<?= $Campsite['SUBDIR'] ?>/plugins/accounting/css/accounting.css" rel="stylesheet" />
<script type="text/javascript" src="<?= $Campsite['SUBDIR'] ?>/plugins/accounting/js/jquery.mtz.monthpicker.js"></script>
<script type="text/javascript" src="<?= $Campsite['SUBDIR'] ?>/plugins/accounting/js/select2/select2.js"></script>
<script type="text/javascript">

$(document).ready(function() {

function prependZero(i) {
    return (i < 10 && i > 0) ? '0'+i : '' + i;
}

// filters handle
$('.smartlist .filters select, .smartlist .filters input').not('.notrigger').change(function(e, data) {
    var smartlist = $(this).closest('.smartlist');
    var smartlistId = smartlist.attr('id').split('-')[1];
    var name = $(this).attr('name');
    var value = $(this).val();
    filters[smartlistId][name] = value;
    if($(this).attr('id') == 'filter_name' || $(this).attr('id') == 'publication_filter' ) {
		filters[smartlistId]['issue'] = 0;
		filters[smartlistId]['section'] = 0;
	}
    if (typeof(data) == 'undefined' || typeof(data.refresh) == 'undefined' || data.refresh == true) {
        tables[smartlistId].fnDraw(true);
    }
    return false;
});

// monthpicker for dates
$('input.monthpicker').monthpicker({
    pattern: 'mm/yyyy',
    monthNames: [
        '<?php echo $translator->trans('Jan', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Feb', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Mar', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Apr', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('May', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Jun', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Jul', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Aug', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Sep', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Oct', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Nov', array(), 'plugin_accounting');?>',
        '<?php echo $translator->trans('Dec', array(), 'plugin_accounting');?>'
    ]
});

$('#filter_date_monthly')
.bind('change', function(e) {
    e.stopImmediatePropagation();
    return false;
})
.monthpicker().bind('monthpicker-click-month', function (e, month) {
    var dateFieldStart = $('#publish_date_from');
    var dateFieldEnd = $('#publish_date_to');
    var data = $(this).val().split('/');
    var year = data[1];
    var dateStart = new Date(year, month-1, 1);
    var dateEnd = new Date(year, month, 0);
    var dateStartValue = dateStart.getFullYear() + "-" + prependZero(dateStart.getMonth() + 1) + "-" + prependZero(dateStart.getDate());
    var dateEndValue = dateEnd.getFullYear() + "-" + prependZero(dateEnd.getMonth() + 1) + "-" + prependZero(dateEnd.getDate());

    dateFieldStart.val(dateStartValue).trigger('change', [{refresh:false}]);
    dateFieldEnd.val(dateEndValue).trigger('change', [{refresh:true}]);
});

// datepicker for dates
$('input.datepicker').datepicker({
    altFormat: 'yy-mm-dd',
    dateFormat: 'dd/mm/yy',
    onSelect: function() {
        var altField = $(this).datepicker('option', 'altField');
        if (altField) {
            $(altField).change();
        }
    }
}).change(function(){
    var altField = $(this).datepicker('option', 'altField');
    if (!$(this).val() && altField) {
        $(altField).val('').change();
    }
});

$('#filter_date_specific_start').datepicker('option', 'altField', '#publish_date_from');
$('#filter_date_specific_end').datepicker('option', 'altField', '#publish_date_to');

// filters managment
$('fieldset.filters .extra').each(function() {
    var extra = $(this);
    $('dl', extra).hide();
    $('<select class="filters"></select>')
        .appendTo(extra)
        .each(function() { // init options
            var select = $(this);
            $('<option value=""><?php echo $translator->trans('Filter by...'); ?></option>')
                .appendTo(select);
            $('dl dt label', extra).each(function() {
                var label = $(this).text();
                $('<option value="'+label+'">'+label+'</option>')
                    .appendTo(select);
            });
        }).change(function() {
            var select = $(this);
            var value = $(this).val();
            $(this).val('');
            $('dl', $(this).parent()).each(function() {
                var label = $('label', $(this)).text();
                var option = $('option[value="' + label + '"]', select);
                if (label == value) {
                    $(this).show();
                    $(this).insertBefore($('select.filters', $(this).parent()));
                    if ($('a', $(this)).length == 0) {
                        $('<a class="detach">X</a>').appendTo($('dd', $(this)))
                            .click(function() {
                                $(this).parents('dl').hide();
                                $('input, select', $(this).parent()).val('').change();
                                select.change();
                                option.show();
                            });
                    }
                    option.hide();
                }
            });
    }); // change
});

$('fieldset.filters').each(function() {
    var fieldset = $(this);
    var divLeft = fieldset.find('div.left');
    var divRight = fieldset.find('div.right');
    var smartlist = fieldset.closest('.smartlist');
    var smartlistId = smartlist.attr('id').split('-')[1];

    // reset all button
    // TODO: proper reset all values and clear textfields
    var resetMsg = '<?php echo $translator->trans('Reset all filters'); ?>';
    $('<a href="#" class="reset button" title="'+resetMsg+'">'+resetMsg+'</a>')
        .appendTo(divRight)
        .click(function() {
            // reset extra filters
            $('.extra dl', fieldset).each(function() {
                $(this).hide();
                $('select, input', $(this)).val('');
            });
            $('select.filters', fieldset).val('');
            $('select.filters option', fieldset).show();
            $('input.filters', fieldset).val('');

            $('input.select2', fieldset).select2('data', null);

            // reset main filters
            $('> select', fieldset).val('0').change();

            // redraw table
            filters[smartlistId] = {};
            tables[smartlistId].fnDraw(true);
            return false;
        });
});

// autocomplete
$('input.select2').each(function() {

    // Default processors
    var idProcessor  = function (data) { return data.id; };
    var resultFormatProcessor = function (data) { return data.name };
    var selectionFormatProcessor = function (data) { return data.name; };
    var paramProcessor = function (term, page) { return { term: term, limit: 20 }; };
    var resultsProcessor = function (data, page) { return {results: data}; };

    switch ($(this).attr('id')) {
        case 'filter_creator':
            resultFormatProcessor = function (data) { return (data.first_name + ' ' + data.last_name).trim(); };
            selectionFormatProcessor = function (data) { return (data.first_name + ' ' + data.last_name).trim(); };
            resultsProcessor = function (data, page) { return {results: data.records}; };
            paramProcessor = function (term, page) { return { queries : { search_name: term }, perPage: 20 }; };
            break;
        case 'filter_topic':
            break;
        case 'filter_author':
            idProcessor  = function (data) { return data.name; };
            break;
        default:
            break;
    }

    $(this).select2({
        minimumInputLength: 1,
        ajax: {
            url: $(this).data('contenturl'),
            dataType: 'json',
            data: paramProcessor,
            results: resultsProcessor
        },
        id: idProcessor,
        formatResult: resultFormatProcessor,
        formatSelection: selectionFormatProcessor,
        formatNoMatches: function() { return "<?php echo $translator->trans('No matches.'); ?>"; },
        formatSearching: function() { return "<?php echo $translator->trans('Searching...'); ?>"; },
        formatInputTooShort: function() { return  "<?php echo $translator->trans('Minimum input of characters: $1', array(1 => '1'), 'plugins_accounting'); ?>"; }
    });
});

$('input.date_selection').click(showDateOption);

function showDateOption() {
    var selVal = $('input[name=date_selection]:checked').val();
    $('*.date-option').hide();

    // Modify visibility of the fields, so they wont be submitted
    // if (selVal == 'monthly') {
    //     $('#filter_date_monthly_start, #filter_date_monthly_end, #filter_date_monthly').show();
    //     $('#filter_date_specific_start, #filter_date_specific_end').hide().val('');
    // } else if (selVal == 'specific') {
    //     $('#filter_date_monthly_start, #filter_date_monthly_end, #filter_date_monthly').hide().val('');
    //     $('#filter_date_specific_start, #filter_date_specific_end').show();
    // }

    $('#publish_date_from').val('');
    $('#publish_date_to').val('');

    $('*.date-option.date-show-'+selVal).show();
}

showDateOption();

}); // document.ready

</script>
		<?php } ?>
