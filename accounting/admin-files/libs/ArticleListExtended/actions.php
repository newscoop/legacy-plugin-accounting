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
<fieldset class="actions">
    <legend><?php echo $translator->trans('Select action'); ?></legend>
    <button id="exportAll"><?php echo $translator->trans('Export all', array(), 'plugin_accounting'); ?></button>
</fieldset>
</div><!-- /.smartlist-actions -->

<?php if (!self::$renderActions) { ?>
<script type="text/javascript">
$(document).ready(function() {

    $('#exportAll').click(function() {
        var smartlistId = $(this).parents('.smartlist:first').attr('id').replace(/smartlist\-/i, '');

        dataTableSettingsExport(smartlistId, function(data) {
            var object = this;
            object.time = new Date().getTime();
            object.form = $('<form action="/admin/accounting/export.php" target="iframe'+object.time+'" method="post" style="display:none;" id="form'+object.time+'"></form>');

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
});

</script>
<?php
}
?>
