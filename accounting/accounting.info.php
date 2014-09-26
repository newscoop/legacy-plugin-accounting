<?php

$info = array(
    'name' => 'accounting',
    'version' => '0.1.0',
    'label' => 'accounting',
    'description' => 'This plugin provides the accounting widget.',
    'menu' => array(
    ),
    'userDefaultConfig' => array(
    ),
    'permissions' => array(
    ),
    'template_engine' => array(
    ),
    'localizer' => array(
    ),
    'no_menu_scripts' => array(),
    'install' => 'plugin_accounting_install',
    'enable' => 'plugin_accounting_install',
    'update' => 'plugin_accounting_update',
    'disable' => 'plugin_accounting_disable',
    'uninstall' => 'plugin_accounting_uninstall'
);

if (!defined('PLUGIN_ACCOUNTING_FUNCTIONS')) {
    define('PLUGIN_ACCOUNTING_FUNCTIONS', TRUE);

    function plugin_accounting_install()
    {
    }

    function plugin_accounting_enable()
    {
        // Copy extension
        $orgExtensionDir = '/plugins/accounting/extensions/accounting/';
        // $GLOBALS['g_campsiteDir']
    }

    function plugin_accounting_disable()
    {
        // Remove extension
    }

    function plugin_accounting_uninstall()
    {
        // Remove extension
    }

    function plugin_accounting_update()
    {
        // Copy & overwrite extension
    }

    function plugin_accounting_init(&$p_context)
    {
    }

    function plugin_accounting_addPermissions()
    {
    }
}
