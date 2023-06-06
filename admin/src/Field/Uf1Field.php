<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Field;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Uf1 Field class.
 *
 * @since  3.8.0
 */
class Uf1Field extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  4.0.0
     */
    protected $type = 'Uf1';

    /**
     * Method to get the field input markup.
     *
     * @return  string    The field input markup.
     *
     * @since   4.0.0
     */
    protected function getInput()
    {
        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();
        $extentionid = $this->id;

        $wa->registerAndUseStyle('com_test1.com_test1-style','com_test1/com_test1-style.css');
		$wa->registerAndUseScript('com_test1.test1-customfield-script', 'com_test1/test1-customfield-script.js');

        Text::script('COM_Test1_AJAX_TEXT');
        $app->getDocument()->addScriptOptions('com_test1', array('CustFieldExtName' => $this->name,'CustFieldExtId' => $extentionid));
        
        $html = array();
        $html[] = '			<div class="input-group">';
        $html[] = '				<input class="form-control" type="text" id="input-text-' . $extentionid.'" name="' . $this->name . '" value="' . $this->value . '">';
        $html[] = '				<button  class="btn btn-primary select-link" id="btn-' . $extentionid.'" data-function="btn-' . $extentionid.'Func" type="button" title="Button title" ><span class="icon-copy"></span></button>';
        $html[] = '			</div>';

        return implode("\n", $html);
    }

}
