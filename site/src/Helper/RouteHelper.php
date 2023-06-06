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

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Language\Multilanguage;

/**
 * Test1  Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_test1
 * @since       __DEPLOY_VERSION__
 */
abstract class RouteHelper
{
	/**
	 * Get the URL route for a Test1 from a view ID, Test1 category ID and language
	 *
	 * @param   integer  $id        The id of the item 
	 * @param   integer  $catid     The id of the Test1's category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the Test1
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getListRoute($id, $catid, $language = 0,$view)
	{
		// Create the link
		$link = 'index.php?option=com_test1&view='.$view.'&id=' . $id;

		if ($catid > 1) {
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Get the URL route for a view from a view ID, Test1 category ID and language
	 *
	 * @param   integer  $id        The id of the Test1
	 * @param   integer  $catid     The id of the Test1's category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the view
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getItemRoute($id, $catid, $language = 0,$view)
	{
		// Create the link
		$link = 'index.php?option=com_test1&view='.$view.'&id=' . $id;

		if ($catid > 1) {
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Get the URL route for a Test1 category from a Test1 category ID and language
	 *
	 * @param   mixed  $catid     The id of the Test1's category either an integer id or an instance of CategoryNode
	 * @param   mixed  $language  The id of the language being used.
	 *
	 * @return  string  The link to the Test1
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof CategoryNode) {
			$id = $catid->id;
		} else {
			$id = (int) $catid;
		}

		if ($id < 1) {
			$link = '';
		} else {
			// Create the link
			$link = 'index.php?option=com_test1&view=category&id=' . $id;

			if ($language && $language !== '*' && Multilanguage::isEnabled()) {
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}
