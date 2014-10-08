<?php
/**
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/ArticleListExtended.php';
require_once WWW_DIR . '/classes/Article.php';
require_once WWW_DIR . '/classes/ArticleType.php';

$list = new ArticleListExtended(TRUE);

// start >= 0
$start = max(0,
    empty($_REQUEST['iDisplayStart']) ? 0 : (int) $_REQUEST['iDisplayStart']);

// results num >= 10 && <= 10000
$limit = min(10000, max(10,
    empty($_REQUEST['iDisplayLength']) ? 0 : (int) $_REQUEST['iDisplayLength']));

// filters - common
$articlesParams = array();
$filters = array(
    'publication' => array('is', 'integer'),
    'issue' => array('is', 'integer'),
    'section' => array('is', 'integer'),
    'language' => array('is', 'integer'),
    'publish_date' => array('is', 'date'),
    'publish_date_from' => array('greater_equal', 'date'),
    'publish_date_to' => array('smaller_equal', 'date'),
    'author' => array('is', 'integer'),
    'topic' => array('is', 'integer'),
    'workflow_status' => array('is', 'string'),
    'creator' => array('is', 'integer'),
    'type' => array('is', 'string'),
);

// mapping form name => db name
$fields = array(
    'publish_date_from' => 'publish_date',
    'publish_date_to' => 'publish_date',
    'language' => 'idlanguage',
    'creator' => 'iduser',
);

foreach ($filters as $name => $opts) {
    if (isset($_REQUEST[$name])
    && (!empty($_REQUEST[$name]) || $_REQUEST[$name] === 0)) {
        $field = !empty($fields[$name]) ? $fields[$name] : $name;
        $articlesParams[] = new ComparisonOperation($field, new Operator($opts[0], $opts[1]), $_REQUEST[$name]);
    }
}

if (empty($_REQUEST['showtype']) || $_REQUEST['showtype'] != 'with_filtered') { // limit articles of filtered types by default

    foreach((array) \ArticleType::GetArticleTypes(true) as $one_art_type_name) {
        $one_art_type = new \ArticleType($one_art_type_name);
        if ($one_art_type->getFilterStatus()) {
            $articlesParams[] = new ComparisonOperation('type', new Operator('not', 'string'), $one_art_type->getTypeName());
        }
    }

}

// Get article types for sorting
$articleTypeFields = $list->getAccountingFields();
$accountFieldSorting = array();

foreach ($articleTypeFields as $field) {
    $accountFieldSorting[] =sprintf('bycustom.num.%s.1', substr($field, 1));
}

// sorting
$cols = $list->getColumnKeys();
$sortOptions = array(
    'Number' => 'bynumber',
    'Order' => 'bysectionorder',
    'Name' => 'byname',
    'Author' => 'byauthor',
    'Comments' => 'bycomments',
    'Reads' => 'bypopularity',
    'CreateDate' => 'bycreationdate',
    'PublishDate' => 'bypublishdate',
    'Status' => 'bystatus',
    'AccountingStatus' => $accountFieldSorting
);

$sortBy = 'bysectionorder';
$sortDir = 'asc';
$sortingCols = min(1, (int) $_REQUEST['iSortingCols']);
for ($i = 0; $i < $sortingCols; $i++) {
    $sortOptionsKey = (int) $_REQUEST['iSortCol_' . $i];
    if (!empty($sortOptions[$cols[$sortOptionsKey]])) {
        $sortBy = $sortOptions[$cols[$sortOptionsKey]];
        $sortDir = $_REQUEST['sSortDir_' . $i];
        $sortParam = array();
        if (!is_array($sortBy)) {
            $sortParam[] = array('field' => $sortBy, 'dir' => $sortDir);
        } else {
            foreach ($sortBy AS $sortByItem) {
                $sortParam[] = array('field' => $sortByItem, 'dir' => $sortDir);
            }
        }
        break;
    }
}

// get articles
$articles = Article::GetList($articlesParams, $sortParam, $start, $limit, $articlesCount, true);

$return = array();
foreach($articles as $article) {
    $return[] = $list->processItem($article);
}
return array(
    'iTotalRecords' => Article::GetTotalCount(),
    'iTotalDisplayRecords' => $articlesCount,
    'sEcho' => (int) $_REQUEST['sEcho'],
    'aaData' => $return,
);
