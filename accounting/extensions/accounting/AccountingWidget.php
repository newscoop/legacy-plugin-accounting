<?php
/**
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
require_once WWW_DIR . '/plugins/accounting/admin-files/libs/ArticleListExtended/ArticleListExtended.php';

/**
 * Media list widget
 * @title Media files
 */
class AccountingWidget extends Widget
{
    public function __construct()
    {
        $this->title = getGS('Accounting');
    }

    public function render()
    {
        camp_load_translation_strings('articles');

        $f_author_id = Input::Get('f_author_id', 'int', null);

        $f_month_id = Input::Get('f_month_f', 'int', null);

        $f_date_start = Input::Get('f_date_start', 'int', null);
        $f_date_end = Input::Get('f_date_end', 'int', null);

        // camp_html_content_top(getGS('Search'), NULL);

        // set up
        $articlelist = new ArticleListExtended();

        // TODO: make universal
        $articlelist->setType('news'); // Only display articles of type news
        $articlelist->setColVis(TRUE);

        $articlelist->setHidden(7);
        $articlelist->setHidden(10);
        $articlelist->setHidden(11);
        $articlelist->setHidden(12);
        $articlelist->setHidden(13);
        $articlelist->setHidden(16);
        $articlelist->setHidden(17);
        $articlelist->setHidden(21);
        $articlelist->setHidden(22);

        // Hide some columns when in small screen
        if (!$this->isFullscreen()) {
            $articlelist->setHidden(14);
        }

        // render
        $articlelist->renderActions();
        $articlelist->renderFiltersAccounting();
        $articlelist->render();

    }
}
