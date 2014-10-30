<?php
/**
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once WWW_DIR . '/admin-files/libs/ArticleList/ArticleList.php';

/**
 * Article list component
 */
class ArticleListExtended extends ArticleList
{
	/** @var string */
    protected $type = null;

	/** @var array */
	protected $filters = array();

	/** @var array */
	protected $orderBy = array();

	/** @var bool */
	protected static $renderFilters = FALSE;

	/** @var bool */
	protected static $renderFiltersAccounting = FALSE;

	/** @var bool */
	protected static $renderActions = FALSE;

	/** @var string */
	protected static $lastId = NULL;

	protected $parameters = array();

	/**
	 * @param bool $randomId
	 */
	public function __construct($randomId = FALSE)
	{
		parent::__construct();

		// generate id - unique per page instance
		if (empty(self::$lastId)) {
			self::$lastId = __FILE__;
			if ($randomId) {
				self::$lastId = uniqid();
			}
		}
		$this->id = substr(sha1(self::$lastId), -6);
		self::$lastId = $this->id;

		// Get parameters from config file
		$this->parameters = \Zend_Registry::get('container')->getParameter('accounting');

		$translator = \Zend_Registry::get('container')->getService('translator');

		// column titles
		$this->cols = array(
            'Number' => NULL,
            'Language' => $translator->trans('Language'),
            'Order' => $translator->trans('Order'),
            'Name' => $translator->trans('Title'),
            'Section' => $translator->trans('Section'),
            'Webcode' => $translator->trans('Webcode'),
            'Type' => $translator->trans('Type'),
            'Created' => $translator->trans('Created by'),
            'Author' => $translator->trans('Author'),
            'Status' => $translator->trans('Status'),
            'OnFrontPage' => $translator->trans('On Front Page'),
            'OnSectionPage' => $translator->trans('On Section Page'),
            'Images' => $translator->trans('Images'),
            'Topics' => $translator->trans('Topics'),
            'Comments' => $translator->trans('Comments'),
            'Reads' => $translator->trans('Reads'),
            'UseMap' => $translator->trans('Use Map'),
            'Locations' => $translator->trans('Locations'),
            'CreateDate' => $translator->trans('Create Date'),
            'PublishDate' => $translator->trans('Publish Date'),
            'LastModified' => $translator->trans('Last Modified'),
		    'Preview' => $translator->trans('Preview'),
		    'Translate' => $translator->trans('Translate'),
		    'AccountingStatus' => $translator->trans('Accounting status', array(), 'plugin_accounting'),
		);
	}

    /**
     * Set type
     *
     * @param string $type
     * @return ArticleList
     */
    public function setType($type)
    {
        $this->type = (string) $type;
        return $this;
    }

	/**
	 * Set filter.
	 * @param string $name
	 * @param mixed $value
	 * @return ArticleList
	 */
	public function setFilter($name, $value)
	{
		$this->filters[$name] = $value;
		return $this;
	}

	/**
	 * Returns columns with or without keys
	 *
	 * @param boolean $full
	 * @return array
	 */
	public function getCols($full = false)
	{
		return ($full) ? $this->cols : array_values($this->cols);
	}

	/**
	 * Set column to order by.
	 *
	 * @param string $column
	 * @param string $direction
	 * @return ArticleList
	 */
	public function setOrderBy($column, $direction = 'asc')
	{
		if (!isset($this->cols[$column])) {
			return $this;
		}

		$columnNo = array_search($column, array_keys($this->cols));
		$this->orderBy[$columnNo] = strtolower($direction) == 'desc' ? 'desc' : 'asc';

		return $this;
	}

	/**
	 * Render actions.
	 * @return ArticleList
	 */
	public function renderActions()
	{
		$this->beforeRender();

		include dirname(__FILE__) . '/actions.php';
		self::$renderActions = TRUE;
		return $this;
	}

	/**
	 * Render accounting filters.
	 * @return ArticleListExtended
	 */
	public function renderFiltersAccounting()
	{
		$this->beforeRender();

		include dirname(__FILE__) . '/filters_accounting.php';
		self::$renderFiltersAccounting = TRUE;
		return $this;
	}

	/**
	 * Render table.
	 * @return ArticleList
	 */
	public function render()
	{
		$this->beforeRender();

		include dirname(__FILE__) . '/table.php';
		self::$renderTable = TRUE;
		echo '</div><!-- /#list-' . $this->id . ' -->';
		return $this;
	}

	/**
	 * Process item
	 * @param Article $article
	 * @return array
	 */
	public function processItem(Article $article)
	{
		global $g_user, $Campsite;

		$translator = \Zend_Registry::get('container')->getService('translator');

		$articleLinkParams = '?f_publication_id=' . $article->getPublicationId()
		. '&amp;f_issue_number=' . $article->getIssueNumber() . '&amp;f_section_number=' . $article->getSectionNumber()
		. '&amp;f_article_number=' . $article->getArticleNumber() . '&amp;f_language_id=' . $article->getLanguageId()
		. '&amp;f_language_selected=' . $article->getLanguageId();
        $articleLinkParamsTranslate = $articleLinkParams.'&amp;f_action=translate&amp;f_action_workflow=' . $article->getWorkflowStatus()
        . '&amp;f_article_code=' . $article->getArticleNumber() . '_' . $article->getLanguageId();
		$articleLink = $Campsite['WEBSITE_URL'].'/admin/articles/edit.php' . $articleLinkParams;
		$previewLink = $Campsite['WEBSITE_URL'].'/admin/articles/preview.php' . $articleLinkParams;
		$htmlPreviewLink = '<a href="'.$previewLink.'" target="_blank" title="'.$translator->trans('Preview').'">'.$translator->trans('Preview').'</a>';
        $translateLink = $Campsite['WEBSITE_URL'].'/admin/articles/translate.php' . $articleLinkParamsTranslate;
        $htmlTranslateLink = '<a href="'.$translateLink.'" target="_blank" title="'.$translator->trans('Translate').'">'.$translator->trans('Translate').'</a>';

		$lockInfo = '';
		$lockHighlight = false;
		$timeDiff = camp_time_diff_str($article->getLockTime());
		if ($article->isLocked() && ($timeDiff['days'] <= 0)) {
			$lockUser = new User($article->getLockedByUser());
			if ($timeDiff['hours'] > 0) {
				$lockInfo = $translator->trans('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
				htmlspecialchars($lockUser->getRealName()),
				htmlspecialchars($lockUser->getUserName()),
				$timeDiff['hours'], $timeDiff['minutes']);
			} else {
				$lockInfo = $translator->trans('The article has been locked by $1 ($2) $3 minute(s) ago.',
				htmlspecialchars($lockUser->getRealName()),
				htmlspecialchars($lockUser->getUserName()),
				$timeDiff['minutes']);
			}
			if ($article->getLockedByUser() != $g_user->getUserId()) {
				$lockHighlight = true;
			}
		}

		$tmpUser = new User($article->getCreatorId());
		$tmpArticleType = new ArticleType($article->getType());

		$authors = array();
		$tmpAuthor = new Author();
		$articleAuthors = ArticleAuthor::GetAuthorsByArticle($article->getArticleNumber(), $article->getLanguageId());
		foreach((array) $articleAuthors as $author) {
			if (strtolower($author->getAuthorType()->getName()) == 'author') {
				$authorName = htmlspecialchars($author->getName());

				if ($this->parameters['transliterate_data']) {
					$authorBio = new AuthorBiography($author->getId(), $this->parameters['transliteration_language_id']);
					$transAuthorName = sprintf('%s %s', $authorBio->getFirstName(), $authorBio->getLastName());

					if (trim($transAuthorName) != '') {
						$authorName = $transAuthorName;
					}
				}

				$authors[] = htmlspecialchars($authorName);
			}
		}
		if (empty($authors) && isset($articleAuthors[0])) {
			$authors[] = $articleAuthors[0];
		}

		$onFrontPage = $article->onFrontPage() ? $translator->trans('Yes') : $translator->trans('No');
		$onSectionPage = $article->onSectionPage() ? $translator->trans('Yes') : $translator->trans('No');

		$imagesNo = (int) ArticleImage::GetImagesByArticleNumber($article->getArticleNumber(), true);
		$topicsNo = (int) ArticleTopic::GetArticleTopics($article->getArticleNumber(), true);
		$commentsNo = '';
		if ($article->commentsEnabled()) {
            global $controller;
            $repositoryComments = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
			$filter = array( 'thread' => $article->getArticleNumber(), 'language' => $article->getLanguageId());
			$params = array( 'sFilter' => $filter);
            $commentsNo = $repositoryComments->getCount($params);
		} else {
			$commentsNo = 'No';
		}

		// get language code
		$language = new Language($article->getLanguageId());

		// Get article data and convert to accounting data
		$accountingData = $this->getAccountingFieldsForArticle($article, true);
		$displayAccountingData = array();
		foreach ($accountingData as $name => $value) {
			if ($value == 0) {
				continue;
			}
			$displayAccountingData[] = $name;
		}

		return array(
		    $article->getArticleNumber(),
		    $article->getLanguageId(),
		    $article->getOrder(),
		    sprintf('%s <a href="%s" title="%s %s">%s</a>',
		    $article->isLocked() ? '<span class="ui-icon ui-icon-locked' . (!$lockHighlight ? ' current-user' : '' ) . '" title="' . $lockInfo . '"></span>' : '',
		    $articleLink,
		    $translator->trans('Edit'), htmlspecialchars($article->getName() . " ({$article->getLanguageName()})"),
		    htmlspecialchars($article->getName() . (empty($_REQUEST['language']) ? " ({$language->getCode()})" : ''))), // /sprintf
		    htmlspecialchars($article->getSection()->getName()),
            $article->getWebcode(),
		    htmlspecialchars($tmpArticleType->getDisplayName()),
		    htmlspecialchars($tmpUser->getRealName()),
		    implode(', ', $authors),
		    $article->getWorkflowStatus(),
		    $onFrontPage,
		    $onSectionPage,
		    $imagesNo,
		    $topicsNo,
		    $commentsNo,
		    (int) $article->getReads(),
		    Geo_Map::GetArticleMapId($article) != NULL ? $translator->trans('Yes') : $translator->trans('No'),
		    (int) sizeof(Geo_Map::GetLocationsByArticle($article)),
		    $article->getCreationDate(),
		    $article->getPublishDate(),
		    $article->getLastModified(),
		    $htmlPreviewLink,
            $htmlTranslateLink,
            implode(', ', $displayAccountingData),
		);
	}

	/**
	 * Retrieve all accounting fields from one or all article types and list by machine or print name
	 * @param string $articleType Get data for one article type or all (null)
	 * @param bool $resolveDisplayName Get display name
	 * @return array
	 */
	public function getAccountingFields($articleType = null, $resolveDisplayName = false)
	{
		$returnData = array();
		$articleTypes = array();

		if (is_null($articleType)) {
			$articleTypeNames = \ArticleType::GetArticleTypes();
			foreach ($articleTypeNames as $articleTypeName) {
				$articleTypes[] = new \ArticleType($articleTypeName);
			}
		} else {
			$articleTypes[] = new \ArticleType($articleType);
		}

		foreach ($articleTypes as $aType) {
			$aColumns = $aType->getUserDefinedColumns();
			foreach ($aColumns as $column) {
				if (strpos($column->getName(), 'Frep_') === false) {
					continue;
				}
				if ($resolveDisplayName) {
					$name = $column->getDisplayName();
				} else {
					$name = $column->getName();
				}
				$returnData[] = $name;
			}
		}

		return $returnData;
	}

	/**
	 * Retrieve all accounting related fields and data
	 * @param Article $article
	 * @return array
	 */
	public function getAccountingFieldsForArticle($article, $resolveDisplayName = false)
	{
		// Get article data and convert to accounting data
		$articleData = $article->getArticleData();
		$accountingData = array();

		foreach ($articleData->getColumnNames() as $name) {
			if (strpos($name, 'Frep_') === false) {
				continue;
			}
			$fieldValue = $articleData->getFieldValue(substr($name, 1));

			if ($resolveDisplayName) {
				$articleTypeField = new ArticleTypeField($article->getType(), substr($name, 1));
				$name = $articleTypeField->getDisplayName();
			} else {
				$name = $name;
			}
			$accountingData[$name] = $fieldValue;
		}

		return $accountingData;
	}

	/**
	 * Handle data
	 * @param array $f_request
	 */
	public function doData($f_request)
	{
		global $ADMIN_DIR, $g_user;
		foreach ($_REQUEST['args'] as $arg) {
			$_REQUEST[$arg['name']] = $arg['value'];
		}
		return require_once dirname(__FILE__) . '/do_data.php';
	}
}

