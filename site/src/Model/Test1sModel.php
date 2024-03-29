<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

/**
 * Test1s model class.
 *
 * @since  __BUMP_VERSION__
 */
class Test1sModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   __BUMP_VERSION__
     */
    public function __construct($config = [])
    {
        
	    if (empty($config['filter_fields'])) {
		    $config['filter_fields'] = [
			    'id', 'a.id',
			    'name', 'a.name',
			    'ordering', 'a.ordering',
		    ];
	    }
        
        parent::__construct($config);
    }
    
	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 */
	public function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();
		// Convert the params field into an object, saving original in _params
		for ($i = 0, $n = count($items); $i < $n; $i++) {
			$item = &$items[$i];
			if (!isset($this->_params)) {
				$item->params = new Registry($item->params);
			}
		}
		return $items;
	}
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string    An SQL query
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getListQuery()
	{
		$user = Factory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'))
			->from($db->quoteName('#__test1', 'a'))
			->whereIn($db->quoteName('a.access'), $groups);

		$query->select('a.published')
			->where('a.published = 1');

		// Filter by state
		$state = $this->getState('filter.published');

		if (is_numeric($state)) {
			$query->where($db->quoteName('a.published') . ' = :published');
			$query->bind(':published', $state, ParameterType::INTEGER);

			// Filter by start and end dates.
			$nowDate = Factory::getDate()->toSql();

			$query->where('(' . $db->quoteName('a.publish_up') .
				' IS NULL OR ' . $db->quoteName('a.publish_up') . ' <= :publish_up)')
				->where('(' . $db->quoteName('a.publish_down') .
					' IS NULL OR ' . $db->quoteName('a.publish_down') . ' >= :publish_down)')
				->bind(':publish_up', $nowDate)
				->bind(':publish_down', $nowDate);
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$language = [Factory::getLanguage()->getTag(), '*'];
			$query->whereIn($db->quoteName('a.language'), $language, ParameterType::STRING);
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_test1');

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		$orderCol = $app->input->get('filter_order', 'ordering');

		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'ordering';
		}

		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');

		if (!in_array(strtoupper($listOrder), ['ASC', 'DESC', ''])) {
			$listOrder = 'ASC';
		}

		$this->setState('list.direction', $listOrder);

		$user = Factory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_test1')) && (!$user->authorise('core.edit', 'com_test1'))) {
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published', 1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}

		$this->setState('filter.language', Multilanguage::isEnabled());

		// Load the parameters.
		$this->setState('params', $params);
	}
    
}
