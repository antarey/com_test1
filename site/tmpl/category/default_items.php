<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use User\Component\Test1\Site\Helper\RouteHelper;

HTMLHelper::_('behavior.core');
?>
<div class="com-test1-test1s_items">
	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
		<?php if (empty($this->items)) : ?>
			<p>
				<?php echo Text::_('JGLOBAL_SELECT_NO_RESULTS_MATCH'); ?>
			</p>
		<?php else : ?>
			<ul class="com-test1-test1s_list category">
				<?php foreach ($this->items as $i => $item) : ?>
					<?php if (in_array($item->access, $this->user->getAuthorisedViewLevels())) : ?>
						<li class="row cat-list-row" >

						<div class="list-title">
							<a href="<?php echo Route::_(RouteHelper::getItemRoute($item->slug, $item->catid, $item->language,'test1')); ?>">
							<?php echo $item->name; ?></a>
						</div>
					</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="com-test1-test1s_pagination">
			<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>

	</form>
</div>
