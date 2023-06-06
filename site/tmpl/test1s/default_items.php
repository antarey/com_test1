<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use User\Component\Test1\Site\Helper\RouteHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$user = Factory::getUser();


?>
<div class="com-test1-test1s-items">
	<?php if (empty($this->items)) : ?>
		<p class="com-test1__message"> <?php echo Text::_('JGLOBAL_SELECT_NO_RESULTS_MATCH'); ?>	 </p>
	<?php else : ?>
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
			<ul class="com-test1-test1s__list">
				<?php foreach ($this->items as $i => $item) : ?>
					<?php if (in_array($item->access, $user->getAuthorisedViewLevels())) : ?>
						<li class="row cat-list-row" >
							<div class="list-title">
								<a href="<?php echo Route::_(RouteHelper::getItemRoute($item->slug, 0, $item->language,'test1')); ?>">
									<?php echo $item->name; ?></a>
							</div>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>

		</form>
	<?php endif; ?>
</div>
