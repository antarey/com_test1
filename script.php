<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Installer\InstallerScript;

/**
 * Script file of Test1 Component
 *
 * @since  __BUMP_VERSION__
 */
class Com_Test1InstallerScript extends InstallerScript
{
	/**
	 * Minimum Joomla version to check
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	private $minimumJoomlaVersion = '4.0';

	/**
	 * Minimum PHP version to check
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	private $minimumPHPVersion = JOOMLA_MINIMUM_PHP;

	/**
	 * Method to install the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  __BUMP_VERSION__
	 */
	public function install($parent): bool
	{
		echo Text::_('COM_TEST1_INSTALLERSCRIPT_INSTALL').'<br>';
		
		$db = Factory::getDbo();
		$alias = ApplicationHelper::stringURLSafe('Test1Uncategorised');

		// Initialize a new category.
		$category = Table::getInstance('Category');

		$data = [
			'extension' => 'com_test1',
			'title' => 'Test1Uncategorised',
			'alias' => $alias ,
			'description' => '',
			'published' => 1,
			'access' => 1,
			'params' => '{"target":"","image":""}',
			'metadesc' => '',
			'metakey' => '',
			'metadata' => '{"page_title":"","author":"","robots":""}',
			'created_time' => Factory::getDate()->toSql(),
			'created_user_id' => (int)$this->getAdminId(),
			'language' => '*',
			'rules' => [],
			'parent_id' => 1,
		];

		$category->setLocation(1, 'last-child');

		// Bind the data to the table
		if (!$category->bind($data)) {
			return false;
		}

		// Check to make sure our data is valid.
		if (!$category->check()) {
			return false;
		}

		// Store the category.
		if (!$category->store(true)) {
			return false;
		}
		
		
		$dashboardId = $this->getDashBoardId();
		if (!$dashboardId)
		{
			$this->addDashboardMenu('test1', 'test1');
		}
		

        return true;
	}

	/**
	 * Method to uninstall the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  __BUMP_VERSION__
	 */
	public function uninstall($parent): bool
	{
		
		$dashboardId = $this->getDashBoardId();
		if ($dashboardId)
		{
			$this->RemoveDashBoard();
		}
		

		$db = Factory::getDBO();
		$query = $db->getQuery( true );
		$db->setQuery("DELETE FROM `#__categories` WHERE `extension` = 'com_test1'; DELETE FROM `#__fields` WHERE `context` LIKE '%com_test1';");
		$db->execute();

		echo Text::_('COM_TEST1_INSTALLERSCRIPT_UNINSTALL').'<br>';
		return true;
	}

	/**
	 * Method to update the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  __BUMP_VERSION__
	 *
	 */
	public function update($parent): bool
	{
		echo Text::_('COM_TEST1_INSTALLERSCRIPT_UPDATE').'<br>';
		
		$dashboardId = $this->getDashBoardId();
		if (!$dashboardId)
		{
			$this->addDashboardMenu('test1', 'test1');
		}
		
        return true;
	}

	/**
	 * Function called before extension installation/update/removal procedure commences
	 *
	 * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  __BUMP_VERSION__
	 *
	 * @throws Exception
	 */
	public function preflight($type, $parent): bool
	{

		$CurDir = __DIR__;
		$file = $CurDir.'/admin/sql/install.mysql.utf8.sql';
		$nowDate = Factory::getDate()->toSql();

		if (file_exists($file)) {
			file_put_contents($file,str_replace('{USER_ID}',(int)$this->getAdminId(),file_get_contents($file)));
			file_put_contents($file,str_replace('CURRENT_TIMESTAMP()',"'".$nowDate."'",file_get_contents($file)));
		}



		if ($type !== 'uninstall') {
			// Check for the minimum PHP version before continuing
			if (!empty($this->minimumPHPVersion) && version_compare(PHP_VERSION, $this->minimumPHPVersion, '<')) {
				Log::add(
					Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPHPVersion),
					Log::WARNING,
					'jerror'
				);

				return false;
			}

			// Check for the minimum Joomla version before continuing
			if (!empty($this->minimumJoomlaVersion) && version_compare(JVERSION, $this->minimumJoomlaVersion, '<')) {
				Log::add(
					Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomlaVersion),
					Log::WARNING,
					'jerror'
				);
				return false;
			}
		}

		echo Text::_('COM_TEST1_INSTALLERSCRIPT_PREFLIGHT').'<br>';
		return true;
	}

	/**
	 * Function called after extension installation/update/removal procedure commences
	 *
	 * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  __BUMP_VERSION__
	 *
	 */
	public function postflight($type, $parent)
	{
		echo Text::_('COM_TEST1_INSTALLERSCRIPT_POSTFLIGHT').'<br>';
		return true;
	}

	/**
	 * Retrieve the admin user id.
	 *
	 * @return  integer|boolean  One Administrator ID.
	 *
	 * @since   __BUMP_VERSION__
	 */
	private function getAdminId()
	{
		$user = Factory::getUser();
		$id = $user->id;
		if (!$id || $id instanceof \Exception) {
			return false;
		}
		return $id;
	}
	/**
 * Retrieve the Test1 dashboard id.
 *
 * @return  integer|boolean  One Dashboard ID.
 *
 * @since   __BUMP_VERSION__
 */
	private function getDashBoardId()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->clear()
			->select($db->quoteName('id'))
			->from($db->quoteName('#__modules'))
			->where("UPPER(" . $db->quoteName('title').") LIKE '%TEST1%'"	);
		$db->setQuery($query);
		$id = $db->loadResult();
		return $id;
	}
	/**
	 * Remove dashboard.
	 *
	 * @since   __BUMP_VERSION__
	 */
	private function RemoveDashBoard()
	{
		$dashboardId = $this->getDashBoardId();

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$conditions = array(
			$db->quoteName('id') . ' = '.$dashboardId
		);
		$query->delete($db->quoteName('#__modules'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();

		$query->clear();
		$conditionsMM = array(
			$db->quoteName('moduleid') . ' = '.$dashboardId
		);
		$query->delete($db->quoteName('#__modules_menu'));
		$query->where($conditionsMM);
		$db->setQuery($query);
		$db->execute();
	}

}
