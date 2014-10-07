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
    'no_menu_scripts' => array(
        '/accounting/admin/author/search.php',
        '/accounting/admin/export.php'
    ),
    'install' => 'plugin_accounting_install',
    'enable' => 'plugin_accounting_enable',
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
        $rootDir = dirname(__FILE__) .'/../../';
        $sourceDir = $rootDir . 'plugins/accounting/extensions/accounting';
        $destDir = $rootDir . 'extensions/accounting';

        mkdir($destDir, 0755);

        foreach (
            $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($destDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $destDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    function plugin_accounting_disable()
    {
        $rootDir = dirname(__FILE__) .'/../../';
        $deleteDir = $rootDir . 'extensions/accounting';
        $iterator = new RecursiveDirectoryIterator($deleteDir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($iterator,
                     RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($deleteDir);
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
