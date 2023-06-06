<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$wa = $this->document->getWebAssetManager();
$app = Factory::getApplication();

$wa->UseStyle('com_test1.test1-style');

$wa->useScript('com_test1.test1-site-script');
Text::script('COM_TEST1_AJAX_TEXT');
$this->document->addScriptOptions('com_test1', array('ParamFromPhp' => '1'));
$function  = $app->input->getCmd('function', 'jAjaxBtnFunc');
$onclick   = $this->escape($function);

$user = Factory::getUser();
//( Array ( ) [core.admin] => 1 [core.options] => 1 [core.manage] => 1 [core.create] => 1 [core.delete] => 1 [core.edit] => 1 [core.edit.state] => 1 [core.edit.own] => 1 [core.edit.value] => 1 )
$canEdit = $user->authorise('core.edit', 'com_test1');

?>

<div>
    <?php echo LayoutHelper::render("joomla.html.image", ["src" => "media/com_test1/images/image.png","width" => "96", "height" => "96", "class" =>"d-flex rubberBand animated"]); ?>
</div>

<div class="container">
    <div class="row" style="margin-top: 6px;">
        <div class="col-md-12"><span>Site list view - <?php echo Text::_('Test1').': '.Text::_('TEST1S');?></span></div>
    </div>
</div>

<div class="container">
    <div class="row" style="margin-right: 0px;margin-left: 0px;margin-top: 6px;">
        <div class="col-md-12 d-md-flex justify-content-md-center">
            <div class="btn-group" role="group">
                <button class="btn btn-primary select-link" type="button" data-function="<?php echo $this->escape($onclick);?>" >Run Ajax</button>
                <button class="btn btn-primary select-link" type="button" data-function="jClearAjaxFunc" >Clear Ajax</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row" style="margin-top: 6px;">
        <div class="col-md-12">
            <div id="DivAjaxResult"></div>
        </div>
    </div>
</div>


<div class="com-test1-test1s">
    <?php echo $this->loadTemplate('items'); ?>

    <div class="com-test1-test1s_pagination w-100">
        <p class="counter float-right pt-3 pr-2">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
</div>
