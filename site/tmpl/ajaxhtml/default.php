<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_testc1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();


$input = $app->input;
$SomeAjaxParam = $input->get('SomeAjaxParam');
$SomeAjaxText = '';
if ($SomeAjaxParam == 1)
{
	$SomeAjaxText = 'The result of processing the parameters that are transferred from Ajax to PHP';
}

?>

<div>The text that is passed from PHP to the Ajax function</div>
<div><?php echo $SomeAjaxText;?></div>

