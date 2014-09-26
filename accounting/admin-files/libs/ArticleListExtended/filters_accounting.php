<?php
/**
 * @package Campsite
 *
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once $GLOBALS['g_campsiteDir'] . '/classes/Author.php';

?>
<div class="filters">
<fieldset class="filters"><legend><?php putGS('Filter'); ?></legend>
    <div class="left">
        <dl>
            <dt><label for="filter_author"><?php putGs('Author'); ?></label></dt>
            <dd><input type="text" name="author" id="filter_author" class="select2 filters" data-contenturl="/admin/users/authors_ajax/search.php" style="width:250px;" /></dd>
        </dl>
    </div>

    <div class="right">
        <div style="display:none">
            <input id="publish_date_from" type="text" name="publish_date_from" class="filters" />
            <input id="publish_date_to" type="text" name="publish_date_to" class="filters" />
        </div>
        <dl>
            <dt><label><?php putGs('Date selection'); ?></label></dt>
            <dd>
                <label>
                    <?php putGs('Per month'); ?>
                    <input type="radio" name="date_selection" value="monthly" class="date_selection" checked>
                </label>
                <label>
                    <?php putGs('Specific dates'); ?>
                    <input type="radio" name="date_selection" value="specific" class="date_selection">
                </label>
            </dd>
        </dl>

        <dl class="date-option date-show-monthly">
            <dt><label for="filter_date_monthly"><?php putGS('Publish date'); ?></label></dt>
            <dd>
                <input id="filter_date_monthly" type="text" name="filter_date_monthly" class="monthpicker notrigger filters" />
            </dd>
        </dl>

        <div class="date-option date-show-specific">
            <dl>
                <dt><label for="filter_date_specific_start"><?php putGS('Publish date start'); ?></label></dt>
                <dd><input id="filter_date_specific_start" type="text" name="filter_date_specific_start" class="datepicker notrigger filters" /></dd>
            </dl>
            <dl>
                <dt><label for="filter_date_specific_end"><?php putGS('Publish date end'); ?></label></dt>
                <dd><input id="filter_date_specific_end" type="text" name="filter_date_specific_end" class="datepicker notrigger filters" /></dd>
            </dl>
        </div>
    </div>

<!--
<div class="extra">
<dl>
	<dt><label for="filter_status"><?php putGS('Status'); ?></label></dt>
	<dd><select name="workflow_status">
		<option value=""><?php putGS('All'); ?></option>
		<option value="published"><?php putGS('Published'); ?></option>
		<option value="new"><?php putGS('New'); ?></option>
		<option value="submitted"><?php putGS('Submitted'); ?></option>
		<option value="withissue"><?php putGS('Publish with issue'); ?></option>
	</select></dd>
</dl>
</div>
-->
</fieldset>
</div>
<!-- /.smartlist-filters -->

		<?php if (!self::$renderFiltersAccounting) { ?>
<link rel="stylesheet" type="text/css" href="/js/select2/select2.css" />
<link type="text/css" href="<?= $Campsite['SUBDIR'] ?>/plugins/accounting/css/accounting.css" rel="stylesheet" />
<script type="text/javascript" src="/js/jquery/jquery.mtz.monthpicker.js"></script>
<script type="text/javascript" src="/js/select2/select2.js"></script>
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
    // TODO: Add localizer
    monthNames: ['<?php putGs('Jan');?>', '<?php putGs('Feb');?>', '<?php putGs('Mar');?>',
    '<?php putGs('Apr');?>', '<?php putGs('May');?>', '<?php putGs('Jun');?>',
    '<?php putGs('Jul');?>', '<?php putGs('Aug');?>', '<?php putGs('Sep');?>',
    '<?php putGs('Oct');?>', '<?php putGs('Nov');?>', '<?php putGs('Dec');?>']
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
            console.log('onSelect trigger change');
            $(altField).change();
        }
    }
    // TODO: add max and min selection options for the dates
}).change(function(){
    var altField = $(this).datepicker('option', 'altField');
    if (!$(this).val() && altField) {
        console.log('change trigger change');
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
            $('<option value=""><?php putGS('Filter by...'); ?></option>')
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
    var smartlist = fieldset.closest('.smartlist');
    var smartlistId = smartlist.attr('id').split('-')[1];

    // reset all button
    // TODO: proper reset all values and clear textfields
    var resetMsg = '<?php putGS('Reset all filters'); ?>';
    $('<a href="#" class="reset" title="'+resetMsg+'">'+resetMsg+'</a>')
        .appendTo(divLeft)
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
        formatNoMatches: function() { return "<?php putGs('No matches.'); ?>"; },
        formatSearching: function() { return "<?php putGs('Searching...'); ?>"; },
        formatInputTooShort: function() { return  "<?php putGs('Minimum input of characters: $1', 1); ?>"; }
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
