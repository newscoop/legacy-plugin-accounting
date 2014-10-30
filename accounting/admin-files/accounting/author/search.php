<?php
/**
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

    $authorService = \Zend_Registry::get('container')->getService('author');

    $limit = ($_REQUEST['limit']) ? $_REQUEST['limit'] : null;
    $term = ($_REQUEST['term']) ? $_REQUEST['term'] : null;

    $authors = $authorService->getAuthors($term, $limit, false);

    header('Content-type: application/json');
    echo json_encode($authors);
