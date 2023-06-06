<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Site\View\Test1s;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Test1s View class
 *
 * @since  __BUMP_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The page parameters
	 *
	 * @var    \Joomla\Registry\Registry|null
	 * @since  __BUMP_VERSION__
	 */
	protected $params = null;

	/**
	 * The page class suffix
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	protected $pageclass_sfx = '';

	/**
	 * The item model state
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  __BUMP_VERSION__
	 */
	protected $state;

	/**
	 * The item details
	 *
	 * @var    \JObject
	 * @since  __BUMP_VERSION__
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    \JPagination
	 * @since  __BUMP_VERSION__
	 */
	protected $pagination;



    /**
     * Method to display the view.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  \Exception on failure, void on success.
     *
     * @since   __BUMP_VERSION__
     */
    public function display($tpl = null)
    {
	    $app    = Factory::getApplication();
	    $params = $app->getParams();

        
	    // Get some data from the models
	    $state      = $this->get('State');
	    $items      = $this->get('Items');
	    $pagination = $this->get('Pagination');

	    // Flag indicates to not add limitstart=0 to URL
	    $pagination->hideEmptyLimitstart = true;

	    // Check for errors.
	    if (count($errors = $this->get('Errors'))) {
		    throw new GenericDataException(implode("\n", $errors), 500);
	    }

	    // Prepare the data.
	    // Compute the test1 slug.
	    for ($i = 0, $n = count($items); $i < $n; $i++) {
		    $item       = &$items[$i];
		    $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
		    $temp       = $item->params;
		    $item->params = clone $params;
		    $item->params->merge($temp);
	    }

	    // Escape strings for HTML output
	    $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'), ENT_COMPAT, 'UTF-8');

	    $maxLevel         = $params->get('maxLevel', -1);
	    $this->maxLevel   = &$maxLevel;
	    $this->state      = &$state;
	    $this->items      = &$items;
	    $this->params     = &$params;
	    $this->pagination = &$pagination;
        
        $this->_prepareDocument();
        return parent::display($tpl);
    }
	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('COM_TEST1_VIEW_TEST1S_DESCRIPTION'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = $app->get('sitename');
		} else if ($app->get('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		} else if ($app->get('sitename_pagetitles', 0) == 2) {
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}
	}


}
