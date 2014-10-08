<?php
/**
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

header('Content-type: application/json');

require_once WWW_DIR . '/classes/ServerRequest.php';

require_once WWW_DIR . '/classes/Extension/WidgetManager.php';
require_once LIBS_DIR . '/ArticleList/ArticleList.php';
require_once WWW_DIR . '/classes/Article.php';
require_once WWW_DIR . '/classes/ArticleData.php';

require_once WWW_DIR . '/plugins/accounting/admin-files/libs/ArticleListExtended/ArticleListExtended.php';

try {
    // init request
    $serverRequest = new ServerRequest($_POST['callback'], isset($_POST['args']) ? $_POST['args'] : array());

    $serverRequest->allow('ArticleListExtended::doAction'); // checked in handler
    $serverRequest->allow('ArticleListExtended::doData');

    // execute
    echo json_encode($serverRequest->execute());
} catch (Exception $e) {
    echo json_encode(array(
        'error_code' => $e->getCode(),
        'error_message' => getGS('Error') . ': ' . $e->getMessage(),
        'error_file' => $e->getFile(),
        'error_line' => $e->getLine(),
    ));
}

exit;

/**
 * Connection check function
 * @return bool
 */
function ping()
{
    return TRUE;
}
