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
$wa->UseStyle('com_test1.test1-style');


$user = Factory::getUser();
//( Array ( ) [core.admin] => 1 [core.options] => 1 [core.manage] => 1 [core.create] => 1 [core.delete] => 1 [core.edit] => 1 [core.edit.state] => 1 [core.edit.own] => 1 [core.edit.value] => 1 )
$canEdit = $user->authorise('core.edit', 'com_test1');
$tparams = $this->item->params;
/*
 * Use item param if exists
 *
 * $itemParamName = $tparams->get('item_param_name');
 * if ($itemParamName) {
 * .....
 * }
 *
 */




?>
<div class="container">
    <div class="row" style="margin-top: 6px;">
        <div class="col-md-12"><span>Site single view - <?php echo Text::_('Test1').': '.Text::_('test1');?></span></div>
    </div>
</div>
<div class="container">
    <div class="row" style="margin-top: 6px;">
        <div class="col-md-12"><span>Site single view - <?php echo $this->item->name;?></span></div>
    </div>
</div>

 
<div>
    <?php echo LayoutHelper::render("joomla.html.image", ["src" => "media/com_test1/images/image.png","width" => "96", "height" => "96", "class" =>"d-flex rubberBand animated"]); ?>
</div>


<?php if ($canEdit) : ?>
    <div class="icons">
        <div class="float-end">
            <div>
                <?php echo HTMLHelper::_('test1icon.edit', $this->item, $tparams); ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php
echo $this->item->event->afterDisplayTitle;
echo $this->item->event->beforeDisplayContent;
echo $this->item->event->afterDisplayContent;
