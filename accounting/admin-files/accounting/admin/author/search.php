<?php

    $authorService = \Zend_Registry::get('container')->getService('author');

    $limit = ($_REQUEST['limit']) ? $_REQUEST['limit'] : null;
    $term = ($_REQUEST['term']) ? $_REQUEST['term'] : null;

    $authors = $authorService->getAuthors($term, $limit, false);

    header('Content-type: application/json');
    echo json_encode($authors);
