<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Controller for single test1 view
 *
 * @since  __DEPLOY_VERSION__
 */
class Test1Controller extends FormController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $view_item = 'form';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel  The model.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getModel($name = 'form', $prefix = '', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, ['ignore_request' => false]);
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function allowAdd($data = [])
	{
		
		if ($categoryId = ArrayHelper::getValue($data, 'catid', $this->input->getInt('catid'), 'int'))
		{
			$user = Factory::getUser();

			// If the category has been passed in the data or URL check it.
			//$user->authorise('core.edit', 'com_test1');
			return $user->authorise('core.create', 'com_test1.category.' . $categoryId);
		}
		
		// In the absence of better information, revert to the component permissions.
		return parent::allowAdd();
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function allowEdit($data = [], $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;

		if (!$recordId) {
			return false;
		}

		// Need to do a lookup from the model.
		$record     = $this->getModel()->getItem($recordId);
		$categoryId = (int) $record->catid;

		if ($categoryId) {
			$user = Factory::getUser();
			// The category has been set. Check the category permissions.
			if ($user->authorise('core.edit', $this->option . '.category.' . $categoryId))
			{
				return true;
			}

			// Fallback on edit.own.
			if ($user->authorise('core.edit.own', $this->option . '.category.' . $categoryId)) {
				return ($record->created_by == $user->id);
			}

			return false;
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function save($key = null, $urlVar = null)
	{
		$result = parent::save($key, $urlVar = null);
		/** @var \Joomla\CMS\MVC\Model\AdminModel $model */
		$model = $this->getModel();
		$this->postSaveHook($model);

		return $result;
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function cancel($key = null)
	{
		$result = parent::cancel($key);

		$this->setRedirect(Route::_($this->getReturnPage(), false));

		return $result;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string    The arguments to append to the redirect URL.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getRedirectToItemAppend($recordId = 0, $urlVar = 'id')
	{
		// Need to override the parent method completely.
		$tmpl = $this->input->get('tmpl');

		$append = '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl=' . $tmpl;
		}

		$append .= '&layout=edit';

		$append .= '&' . $urlVar . '=' . (int) $recordId;

		$itemId = $this->input->getInt('Itemid');
		$return = $this->getReturnPage();
		$catId  = $this->input->getInt('catid');

		if ($itemId) {
			$append .= '&Itemid=' . $itemId;
		}

		if ($catId) {
			$append .= '&catid=' . $catId;
		}

		if ($return) {
			$append .= '&return=' . base64_encode($return);
		}

		return $append;
	}

	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return  string    The return URL.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getReturnPage()
	{
		$return = $this->input->get('return', null, 'base64');

		if (empty($return) || !Uri::isInternal(base64_decode($return))) {
			return Uri::base();
		}

		return base64_decode($return);
	}

	protected function postSaveHook(BaseDatabaseModel $model, $validData = array())
	{
		$id = $model->getState($model->getName() . '.id');
		$itemId = $this->input->getInt('Itemid');
		$tmpl = $this->input->get('tmpl');
		$return = $this->getReturnPage();
		$catId  = $this->input->getInt('catid');
		$parentViewName = $this->getName();
		$componentName = $this->input->get('option');
		$redirectUrl = 'index.php?option='.$componentName;

		if ($tmpl) {
			$redirectUrl .= '&tmpl=' . $tmpl;
		}

		$redirectUrl .= '&id=' . (int) $id;

		if ($itemId) {
			$redirectUrl .= '&Itemid=' . $itemId;
		}

		if ($catId) {
			$redirectUrl .= '&catid=' . $catId;
		}

		$redirectUrl .= '&view=' . $parentViewName;

		if ($return) {
			$redirectUrl .= '&return=' . base64_encode($return);
		}

		if ($id){
			//$this->app->enqueueMessage('error', 'error');
			$this->setRedirect(Route::_($redirectUrl, false));
		}

	}

}
