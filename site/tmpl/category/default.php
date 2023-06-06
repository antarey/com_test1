<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
?>

<div class="com-test1-category">
	<?php
		$this->subtemplatename = 'items';
		echo LayoutHelper::render('joomla.content.category_default', $this);
	?>
</div>
