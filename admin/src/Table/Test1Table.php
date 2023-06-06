<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;

/**
 * Test1 Table class for Test1.
 *
 * @since  __BUMP_VERSION__
 */
class Test1Table extends Table implements TaggableTableInterface
{
	use TaggableTableTrait;

	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_test1.test1';

		parent::__construct('#__test1', 'id', $db);
	}

	/**
	 * Generate a valid alias from title / date.
	 * Remains public to be able to check for duplicated alias before saving
	 *
	 * @return  string
	 */
	public function generateAlias()
	{
		if (empty($this->alias)) {
			$this->alias = $this->name . '-' . Factory::getDate()->format('v');
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '') {
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return $this->alias;
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 *
	 * @see     Table::check
	 * @since   __BUMP_VERSION__
	 */
	public function check()
	{

        $user = Factory::getUser();
        $date   = Factory::getDate()->toSql();

		try {
			parent::check();
		} catch (\Exception $e) {
			$this->setError($e->getMessage());

			return false;
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
			$this->setError(Text::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));

			return false;
		}

		// Set publish_up, publish_down to null if not set
		if (!$this->publish_up) {
			$this->publish_up = null;
		}

		if (!$this->publish_down) {
			$this->publish_down = null;
		}

        if (!$this->created) {
            $this->created = $date;
        }

        if (!$this->created_by) {
            $this->created_by = $user->id;
        }
        if (!$this->created_by_alias) {
            $this->created_by_alias = ApplicationHelper::stringURLSafe($user->username);
        }
        if (!$this->modified) {
            $this->modified = $date;
        }
        if (!$this->modified_by) {
            $this->modified_by = $user->id;
        }

		if (!$this->params) {
			$this->params = '{}';
		}

		return true;
	}

	/**
	 * Get the type alias
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}

	/** Stores a test1.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function store($updateNulls = true)
	{
        $date   = Factory::getDate()->toSql();
        $userId = Factory::getUser()->id;

		// Transform the params field
		if (is_array($this->params)) {
			$registry = new Registry($this->params);
			$this->params = (string) $registry;
		}

        if (!(int) $this->created) {
            $this->created = $date;
        }

        if (empty($this->created_by)) {
            $this->created_by = $userId;
        }

        if (!$this->created_by_alias) {
            $this->created_by_alias = ApplicationHelper::stringURLSafe($user->username);
        }

		if (!$this->params) {
			$this->params = '{}';
		}		

        $this->modified = $date;
        $this->modified_by = $userId;


		return parent::store($updateNulls);
	}
}
