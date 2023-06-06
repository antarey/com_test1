<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\Component\Categories\Administrator\Helper\CategoryAssociationHelper;
use User\Component\Test1\Site\Helper\RouteHelper;

/**
 * Test1  Component Association Helper
 *
 * @since  __BUMP_VERSION__
 */
abstract class AssociationHelper extends CategoryAssociationHelper
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  __BUMP_VERSION__
	 */
	public static function getAssociations($id = 0, $view = null)
	{
		$jinput = Factory::getApplication()->input;
		$view = $view ?? $jinput->get('view');
		$id = empty($id) ? $jinput->getInt('id') : $id;


		if ($view === 'category' || $view === 'categories') {
			return self::getCategoryAssociations($id, 'com_test1');
		}
		else {
			if ($id) {
				$associations = Associations::getAssociations('com_test1', '#__test1', 'com_test1.item', $id);

				$return = [];

				foreach ($associations as $tag => $item) {
					$return[$tag] = RouteHelper::getListRoute($item->id, (int) $item->catid, $item->language,$view);
				}

				return $return;
			}

		}

		return [];
	}
}
