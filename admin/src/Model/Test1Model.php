<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

/**
 * Item Model for a test1.
 *
 * @since  __BUMP_VERSION__
 */
class Test1Model extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	public $typeAlias = 'com_test1.test1';

	/**
	 * The context used for the associations table
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	protected $associationsContext = 'com_test1.item';

	/**
	 * Batch copy/move command. If set to false, the batch copy/move command is not supported
	 *
	 * @var  string
	 */
    protected $batch_copymove = 'category_id';

	/**
	 * Allowed batch commands
	 *
	 * @var array
	 */
	protected $batch_commands = [
		'assetgroup_id' => 'batchAccess',
		'language_id'   => 'batchLanguage',
		'user_id'       => 'batchUser',
	];

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function getForm($data = [], $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm($this->typeAlias, 'test1', ['control' => 'jform', 'load_data' => $loadData]);

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function loadFormData()
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_test1.edit.test1.data', []);

		if (empty($data)) {
			$data = $this->getItem();
            
			// Prime some default values.
			if ($this->getState('test1.id') == 0) {
				$data->set('catid', $app->input->get('catid', $app->getUserState('com_test1.test1s.filter.category_id'), 'int'));
			}
		}

		$this->preprocessData($this->typeAlias, $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// Load associated test1 items
		$assoc = Associations::isEnabled();

		if ($assoc) {
			$item->associations = [];

			if ($item->id != null) {
				$associations = Associations::getAssociations('com_test1', '#__test1', 'com_test1.item', $item->id, 'id', null);

				foreach ($associations as $tag => $association) {
					$item->associations[$tag] = $association->id;
				}
			}
		}


		return $item;
	}

	/**
	 * Preprocess the form.
	 *
	 * @param   \JForm  $form   Form object.
	 * @param   object  $data   Data object.
	 * @param   string  $group  Group name.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		if (Associations::isEnabled()) {
			$languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if (count($languages) > 1) {
				$addform = new \SimpleXMLElement('<form />');
				$fields = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');
				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');

				foreach ($languages as $language) {
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $language->lang_code);
					$field->addAttribute('type', 'modal_test1');
					$field->addAttribute('language', $language->lang_code);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('select', 'true');
					$field->addAttribute('new', 'true');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}

				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   \Joomla\CMS\Table\Table  $table  The Table object
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function prepareTable($table)
	{
		$table->generateAlias();
	}
}
