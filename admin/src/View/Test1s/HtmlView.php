<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\View\Test1s;

\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;

/**
 * View class for a list of test1s.
 *
 * @since  __BUMP_VERSION__
 */
class HtmlView extends BaseHtmlView
{

    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var  \JPagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var  \JObject
     */
    protected $state;

    /**
     * Form object for search filters
     *
     * @var  \JForm
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var  array
     */
    public $activeFilters;

    /**
     * Method to display the view.
     *
     * @param string $tpl A template file to load. [optional]
     *
     * @return  void
     *
     * @since   __BUMP_VERSION__
     */

	public function display($tpl = null): void
    {

        $this->items = $this->get('Items');

        $this->pagination = $this->get('Pagination');

        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->state = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // Preprocess the list of items to find ordering divisions.
        // TODO: Complete the ordering stuff with nested sets
        foreach ($this->items as &$item) {
            $item->order_up = true;
            $item->order_dn = true;
        }

        if (!count($this->items) && $this->get('IsEmptyState')) {
            $this->setLayout('emptystate');
        }

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
            $this->sidebar = \JHtmlSidebar::render();
        } else {
            // In article associations modal we need to remove language filter if forcing a language.
            // We also need to change the category filter to show show categories with All or the forced language.
            if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD')) {
                // If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
                $languageXml = new \SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
                $this->filterForm->setField($languageXml, 'filter', true);

                // Also, unset the active language filter so the search tools is not open by default with this filter.
                unset($this->activeFilters['language']);
                
                // One last changes needed is to change the category filter to just show categories with All language or with the forced language.
                $this->filterForm->setFieldAttribute('category_id', 'language', '*,' . $forcedLanguage, 'filter');
            }
        }


        $user = Factory::getUser();
        $toolbar = Toolbar::getInstance('toolbar');
        ToolbarHelper::title(Text::_('COM_TEST1_MANAGER_TEST1'), 'generic');

        if ($user->authorise('core.admin', 'com_test1') || $user->authorise('core.options', 'com_test1')) {
            if ($this->getLayout() !== 'modal') {
                //$this->sidebar = \JHtmlSidebar::render();
                $toolbar->preferences('com_test1');
                ToolbarHelper::divider();
            }
        }

	    parent::display($tpl);
    }

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function addToolbar()
	{
		$this->sidebar = \JHtmlSidebar::render();

		$canDo = ContentHelper::getActions('com_test1', '', $this->state->get('id'));
		$user  = Factory::getUser();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');


        ToolbarHelper::title(Text::_('COM_TEST1_MANAGER_TEST1'), 'generic');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_test1', 'core.create')) > 0) {
			$toolbar->addNew('test1.add');
		}

		if ($canDo->get('core.edit.state')) {
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('far fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);
			$childBar = $dropdown->getChildToolbar();
			$childBar->publish('test1s.publish')->listCheck(true); /* foos.publish - можливо потрібно SYSTEMNAME_ALL_LOWER */
			$childBar->unpublish('test1s.unpublish')->listCheck(true);

			$childBar->archive('test1s.archive')->listCheck(true);

			if ($user->authorise('core.admin')) {
				$childBar->checkin('test1s.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2) {
				$childBar->trash('test1s.trash')->listCheck(true);
			}

			if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
				$childBar->delete('test1s.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}

			/*// Add a batch button
			if ($user->authorise('core.create', 'com_test1')
				&& $user->authorise('core.edit', 'com_test1')
				&& $user->authorise('core.edit.state', 'com_test1')) {
				$childBar->popupButton('batch')
					->text('JTOOLBAR_BATCH')
					->selector('collapseModal')
					->listCheck(true);
			}*/
		}

		ToolbarHelper::divider();
		ToolbarHelper::help('', false, 'http://example.org');
	}

}
