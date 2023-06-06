<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;

/**
 * Test1 Component Category Tree
 *
 * @since  __BUMP_VERSION__
 */
class Category extends Categories
{
	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function __construct($options = [])
	{
		$options['table']      = '#__test1';
		$options['extension']  = 'com_test1';
		$options['statefield'] = 'published';

		parent::__construct($options);
	}
}
