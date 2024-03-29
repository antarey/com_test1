<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Association\AssociationExtensionHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Table\Table;
use User\Component\Test1\Site\Helper\AssociationHelper;

/**
 * Test1 associations helper.
 *
 * @since  __BUMP_VERSION__
 */
class AssociationsHelper extends AssociationExtensionHelper
{
	/**
	 * The extension name
	 *
	 * @var     array   $extension
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected $extension = 'com_test1';

	/**
	 * Array of item types
	 *
	 * @var     array   $itemTypes
	 *
	 * @since   __BUMP_VERSION__
	 */
//	protected $itemTypes = ['test1', 'category'];

	/**
	 * Has the extension association support
	 *
	 * @var     boolean   $associationsSupport
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected $associationsSupport = true;

	/**
	 * Method to get the associations for a given item.
	 *
	 * @param   integer  $id    Id of the item
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  __BUMP_VERSION__
	 */
	public function getAssociationsForItem($id = 0, $view = null)
	{
		return AssociationHelper::getAssociations($id, $view);
	}

	/**
	 * Get the associated items for an item
	 *
	 * @param   string  $typeName  The item type
	 * @param   int     $id        The id of item for which we need the associated items
	 *
	 * @return  array
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function getAssociations($typeName, $id)
	{
		$type = $this->getType($typeName);

		$context    = $this->extension . '.item';
		$catidField = 'catid';

		if ($typeName === 'category') {
			$context    = 'com_categories.item';
			$catidField = '';
		}

		// Get the associations.
		$associations = Associations::getAssociations(
			$this->extension,
			$type['tables']['a'],
			$context,
			$id,
			'id',
			'alias',
			$catidField
		);

		return $associations;
	}

	/**
	 * Get item information
	 *
	 * @param   string  $typeName  The item type
	 * @param   int     $id        The id of item for which we need the associated items
	 *
	 * @return  Table|null
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function getItem($typeName, $id)
	{
		if (empty($id)) {
			return null;
		}

		$table = null;


		if ($typeName === 'category' ) {
			$table = Table::getInstance('Category');
		}
		else {
			$table = Table::getInstance('Test1Table', 'User\\Component\\Test1\\Administrator\\Table\\');
		}

		if (empty($table)) {
			return null;
		}

		$table->load($id);

		return $table;
	}

	/**
	 * Get information about the type
	 *
	 * @param   string  $typeName  The item type
	 *
	 * @return  array  Array of item types
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function getType($typeName = '')
	{
		$fields  = $this->getFieldsTemplate();
		$tables  = [];
		$joins   = [];
		$support = $this->getSupportTemplate();
		$title   = '';

		if ($typeName === 'category'){
			$fields['created_user_id'] = 'a.created_user_id';
			$fields['ordering'] = 'a.lft';
			$fields['level'] = 'a.level';
			$fields['catid'] = '';
			$fields['state'] = 'a.published';

			$support['state'] = true;
			$support['acl'] = true;
			$support['checkout'] = false;
			$support['level'] = false;

			$tables = [
				'a' => '#__categories'
			];

			$title = 'category';

		}
		else {
			$fields['title'] = 'a.name';
			$fields['state'] = 'a.published';

			$support['state'] = true;
			$support['acl'] = true;
			$support['category'] = true;
			$support['save2copy'] = true;

			$tables = [
				'a' => '#__test1'
			];

			$title = 'test1';			
		}
		

		return [
			'fields'  => $fields,
			'support' => $support,
			'tables'  => $tables,
			'joins'   => $joins,
			'title'   => $title
		];
	}

	/**
	 * Get default values for fields array
	 *
	 * @return  array
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getFieldsTemplate()
	{
		return [
			'id'                  => 'a.id',
			'title'               => 'a.title',
			'alias'               => 'a.alias',
			'ordering'            => 'a.id',
			'menutype'            => '',
			'level'               => '',
			'catid'               => 'a.catid',
			'language'            => 'a.language',
			'access'              => 'a.access',
			'state'               => 'a.state',
			'created_user_id'     => '',
			'checked_out'         => '',
			'checked_out_time'    => ''
		];
	}
}
