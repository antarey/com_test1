<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Service\HTML;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use User\Component\Test1\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;

/**
 * Content Component HTML Helper
 *
 * @since  __DEPLOY_VERSION__
 */
class Icon
{
	/**
	 * The application
	 *
	 * @var    CMSApplication
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $application;

	/**
	 * Service constructor
	 *
	 * @param   CMSApplication  $application  The application
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(CMSApplication $application)
	{
		$this->application = $application;
	}

	/**
	 * Method to generate a link to the create item page for the given category
	 *
	 * @param   object    $category  The category information
	 * @param   Registry  $params    The item parameters
	 * @param   array     $attribs   Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the create item link
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function create($category, $params, $attribs = [])
	{
		$uri = Uri::getInstance();
		$jinput = Factory::getApplication()->input;
		$view = $jinput->get('view');

		$categoryId = '';
		$categoryId = '&catid=' . $category->id;
		$url = 'index.php?option=com_test1&task='.$view.'.add&return=' . base64_encode($uri) . '&id=0'.$categoryId;

		$text = LayoutHelper::render('joomla.content.icons.create', ['params' => $params, 'legacy' => false]);

		// Add the button classes to the attribs array
		if (isset($attribs['class'])) {
			$attribs['class'] .= ' btn btn-primary';
		} else {
			$attribs['class'] = 'btn btn-primary';
		}

		$button = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		$output = '<span class="hasTooltip" title="' . HTMLHelper::_('tooltipText', 'COM_TEST1_MANAGER_SINGLE_NEW') . '">' . $button . '</span>';

		return $output;
	}

	/**
	 * Display an edit icon for the view.
	 *
	 * This icon will not display in a popup window, nor if the item is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   object    $singleItem  The item information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string   The HTML for the item edit icon.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function edit($singleItem, $params, $attribs = [], $legacy = false)
	{
		$user = Factory::getUser();
		$uri  = Uri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return '';
		}

		// Ignore if the state is negative (trashed).
		if ($singleItem->published < 0) {
			return '';
		}

		// Set the link class
		$attribs['class'] = 'dropdown-item';

		// Show checked_out icon if the item is checked out by a different user
		if (property_exists($singleItem, 'checked_out')
			&& property_exists($singleItem, 'checked_out_time')
			&& $singleItem->checked_out > 0
			&& $singleItem->checked_out != $user->get('id')) {
			$checkoutUser = Factory::getUser($singleItem->checked_out);
			$date         = HTMLHelper::_('date', $singleItem->checked_out_time);
			$tooltip      = Text::_('JLIB_HTML_CHECKIN') . ' :: ' . Text::sprintf('COM_TEST1_BY_USER', $checkoutUser->name)
				. ' <br /> ' . $date;

			$text = LayoutHelper::render('joomla.content.icons.edit_lock', ['tooltip' => $tooltip, 'legacy' => $legacy]);

			$output = HTMLHelper::_('link', '#', $text, $attribs);

			return $output;
		}

		if (!isset($singleItem->slug)) {
			$singleItem->slug = "";
		}
		$jinput = Factory::getApplication()->input;
		$view = $jinput->get('view');

		$singleItemUrl = RouteHelper::getItemRoute($singleItem->slug, $singleItem->catid, $singleItem->language,$view);
		$url        = $singleItemUrl . '&task='.$view.'.edit&id=' . $singleItem->id . '&return=' . base64_encode($uri);

		if ($singleItem->published == 0) {
			$overlib = Text::_('JUNPUBLISHED');
		} else {
			$overlib = Text::_('JPUBLISHED');
		}

		if (!isset($singleItem->created)) {
			$date = HTMLHelper::_('date', 'now');
		} else {
			$date = HTMLHelper::_('date', $singleItem->created);
		}

		if (!isset($created_by_alias) && !isset($singleItem->created_by)) {
			$author = '';
		} else {
			$author = $singleItem->created_by_alias ?: Factory::getUser($singleItem->created_by)->name;
		}

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= Text::sprintf('COM_TEST1_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		
		if (strtotime($singleItem->publish_up) > strtotime(Factory::getDate())
			|| ((strtotime($singleItem->publish_down) < strtotime(Factory::getDate())) && $singleItem->publish_down != Factory::getDbo()->getNullDate())) {
			$icon = 'eye-slash';
		}

		$text = '<span class="hasTooltip fas fa-pen" title="'
			. HTMLHelper::tooltipText(Text::_('COM_TEST1_MANAGER_SINGLE_EDIT'), $overlib, 0, 0) . '"></span> ';
		$text .= Text::_('JGLOBAL_EDIT');

		$attribs['title'] = Text::_('COM_TEST1_MANAGER_SINGLE_EDIT');
		$output           = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}
}

