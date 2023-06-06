<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Field\Modal;

\defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Supports a modal test1 picker.
 *
 * @since  __DEPLOY_VERSION__
 */
class Test1Field extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   __DEPLOY_VERSION__
	 */
	protected $type = 'Modal_Test1';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   __DEPLOY_VERSION__ 
	 */
	protected function getInput()
	{
		$allowClear  = ((string) $this->element['clear'] != 'false');
		$allowSelect = ((string) $this->element['select'] != 'false');

		// The active test1 id field.
		$value = (int) $this->value > 0 ? (int) $this->value : '';

		// Create the modal id.
		$modalId = 'Test1_' . $this->id;

		// Add the modal field script to the document head.
		/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

		// Add the modal field script to the document head.
		$wa->useScript('field.modal-fields');

		// Script to proxy the select modal function to the modal-fields.js file.
		if ($allowSelect) {
			static $scriptSelect = null;

			if (is_null($scriptSelect)) {
				$scriptSelect = [];
			}

			if (!isset($scriptSelect[$this->id])) {
				$wa->addInlineScript("
				window.jSelectTest1_" . $this->id . " = function (id, title, object) {
					window.processModalSelect('Test1', '" . $this->id . "', id, title, '', object);
				}",
					[],
					['type' => 'module']
				);

				$scriptSelect[$this->id] = true;
			}
		}

		// Setup variables for display.
		$linkTest1s = 'index.php?option=com_test1&amp;view=test1s&amp;layout=modal&amp;tmpl=component&amp;'
			. Session::getFormToken() . '=1';
		$modalTitle   = Text::_('COM_TEST1_MANAGER_SINGLE_EDIT');

		if (isset($this->element['language'])) {
			$linkTest1s .= '&amp;forcedLanguage=' . $this->element['language'];
			$modalTitle .= ' &#8212; ' . $this->element['label'];
		}

		$urlSelect = $linkTest1s . '&amp;function=jSelectTest1_' . $this->id;

		if ($value) {
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('name'))
				->from($db->quoteName('#__test1'))
				->where($db->quoteName('id') . ' = ' . (int) $value);
			$db->setQuery($query);

			try {
				$title = $db->loadResult();
			} catch (\RuntimeException $e) {
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		$title = empty($title) ? Text::_('COM_TEST1_SELECT_ITEM_LABEL') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current test1 display field.
		$html  = '';

		if ($allowSelect || $allowNew || $allowEdit || $allowClear) {
			$html .= '<span class="input-group">';
		}

		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" readonly size="35">';

		// Select test1 button
		if ($allowSelect) {
			$html .= '<button'
				. ' class="btn btn-primary hasTooltip' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_select"'
				. ' data-bs-toggle="modal"'
				. ' type="button"'
				. ' data-bs-target="#ModalSelect' . $modalId . '"'
				. ' title="' . HTMLHelper::tooltipText('COM_TEST1_MANAGER_SINGLE_EDIT') . '">'
				. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
				. '</button>';
		}

		// Clear test1 button
		if ($allowClear) {
			$html .= '<button'
				. ' class="btn btn-secondary' . ($value ? '' : ' hidden') . '"'
				. ' id="' . $this->id . '_clear"'
				. ' type="button"'
				. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
				. '<span class="icon-remove" aria-hidden="true"></span>' . Text::_('JCLEAR')
				. '</button>';
		}

		if ($allowSelect || $allowNew || $allowEdit || $allowClear) {
			$html .= '</span>';
		}

		// Select test1 modal
		if ($allowSelect) {
			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'ModalSelect' . $modalId,
				[
					'title'       => $modalTitle,
					'url'         => $urlSelect,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
										. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
				]
			);
		}

		// Note: class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="'
			. $this->id . '_id"'
			. $class . ' data-required="' . (int) $this->required
			. '" name="' . $this->name
			. '" data-text="'
			. htmlspecialchars(Text::_('COM_TEST1_SELECT_ITEM_LABEL', true), ENT_COMPAT, 'UTF-8')
			. '" value="' . $value . '">';

		return $html;
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getLabel()
	{
		return str_replace($this->id, $this->id . '_name', parent::getLabel());
	}
}
