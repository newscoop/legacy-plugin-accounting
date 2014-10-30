<?php

$info = array(
    'name' => 'accounting',
    'version' => '0.10.0',
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
        'id' => 'plugin_accounting',
        'path' => '/plugins/accounting/admin-files/accounting/*/*',
        'screen_name' => 'accounting'
    ),
    'no_menu_scripts' => array(
        '/accounting/author/search.php',
        '/accounting/export.php',
        '/accounting/json.php'
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

        try {
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
        } catch (\Exception $e) {
            throw new \Exception('Could not install extension properly. Please check the permissions or copy the extension manually.');
        }
    }

    function plugin_accounting_disable()
    {
        $rootDir = dirname(__FILE__) .'/../../';
        $deleteDir = $rootDir . 'extensions/accounting';
        try {
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
        } catch (\Exception $e) {
        }
    }

    function plugin_accounting_uninstall()
    {
        $rootDir = dirname(__FILE__) .'/../../';
        $deleteDir = $rootDir . 'extensions/accounting';
        try {
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
        } catch (\Exception $e) {
        }
    }

    function plugin_accounting_update()
    {
        plugin_accounting_disable();
        plugin_accounting_enable();
    }

    function plugin_accounting_init(&$p_context)
    {
    }

    function plugin_accounting_addPermissions()
    {
    }
}
