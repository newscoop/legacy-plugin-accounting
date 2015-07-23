<?php
/**
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
?>
<div class="actions">
<?php
	global $g_user;
    $translator = \Zend_Registry::get('container')->getService('translator');
?>
<fieldset class="actions accounting-actions">
    <legend><?php echo $translator->trans('Select action'); ?></legend>
    <small id="exportHelp" data-original-title="" title="" class="button-help">(?)</small>
    <label>
        <button id="exportSelect" class="button"><?php
            echo $translator->trans('Export selected rows', array(), 'plugin_accounting');
        ?></button>
    </label>
    <label>
        <button id="exportAll" class="button"><?php
            echo $translator->trans('Export all rows', array(), 'plugin_accounting');
        ?></button>
    </label>
</fieldset>
</div><!-- /.smartlist-actions -->

<?php if (!self::$renderActions) { ?>
<script src="/bundles/newscoopnewscoop/js/popover.js" type="text/javascript"></script>
<script type="text/javascript">

function popover(target, content) {
    target.popover({
        trigger: 'hover',
        content : content,
        html: true,
        placement: 'left',
        container: 'body'
    });
};

popover($('#exportHelp'), '<?php
    echo $translator->trans('<i>Export all rows</i>: exports all rows (including all pages) returned by the filter action.', array(), 'plugin_accounting');
    echo '<br>';
    echo $translator->trans('<i>Export selected rows</i>: exports only rows of which the checkbox has been selected.', array(), 'plugin_accounting');
 ?>');

$(document).ready(function() {

    $('#exportAll, #exportSelect').click(function() {
        var additionalData = [];
        var buttonId = $(this).attr('id');
        var smartlistId = $(this).parents('.smartlist:first').attr('id').replace(/smartlist\-/i, '');

        if (buttonId == 'exportSelect') {
            // add selected rows to data
            var table = $('#table-'+smartlistId);
            var rows = table.find('tr.selected').find('td.id').find('input:checkbox[name!=""]');

            if (rows.length == 0) {
                alert('<?php echo $translator->trans('Please make a selection of rows to continue or use the export all button!', array(), 'plugin_accounting'); ?>');
                return false;
            }

            rows.each(function() {
                additionalData.push($(this).attr('name'));
            });
        }

        dataTableSettingsExport(smartlistId, function(data) {
            var object = this;
            object.time = new Date().getTime();
            object.form = $('<form action="/admin/accounting/export.php" target="iframe'+object.time+'" method="post" style="display:none;" id="form'+object.time+'"></form>');

            data.push({
                'name' : 'number',
                'value' : additionalData.join(',')
            });

            $("<input type='hidden' />")
             .attr("name", 'export_data')
             .attr("value", JSON.stringify(data))
             .appendTo(object.form);

            var iframe = $('<iframe data-time="'+object.time+'" style="display:none;" id="iframe'+object.time+'"></iframe>');
            $( "body" ).append(iframe);
            $( "body" ).append(object.form);
            object.form.submit();
            iframe.load(function(){  $('#form'+$(this).data('time')).remove();  $(this).remove();   });
        });
    });

    // check all/none
    $('.smartlist thead input:checkbox').change(function() {
        var smartlist = $(this).closest('.smartlist');
        var checked = (typeof $(this).attr("checked") === 'undefined') ? false : true;
        $('tbody input:checkbox', smartlist).each(function() {
            $(this).attr('checked', checked);
            if (checked) {
                $(this).parents('tr').addClass('selected');
            } else {
                $(this).parents('tr').removeClass('selected');
            }
        });
    });
});

</script>
<?php
}
?>
