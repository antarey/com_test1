<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_TEST1',
	'formURL' => 'index.php?option=com_test1',
	'helpURL' => 'https://github.com/astridx/boilerplate#readme',
	'icon' => 'icon-copy',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_test1') || count($user->getAuthorisedCategories('com_test1', 'core.create')) > 0) {
	$displayData['createURL'] = 'index.php?option=com_test1&task=test1.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
