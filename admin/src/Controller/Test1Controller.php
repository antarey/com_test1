<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

/**
 * Controller for a single Test1
 *
 * @since  __BUMP_VERSION__
 */
class Test1Controller extends FormController
{
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function batch($model = null)
	{
		$this->checkToken();

		$model = $this->getModel('Test1', 'Administrator', []);

		// Preset the redirect
		$this->setRedirect(Route::_('index.php?option=com_test1&view=test1' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
