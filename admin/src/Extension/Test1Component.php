<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use User\Component\Test1\Administrator\Service\HTML\AdministratorService;

use Psr\Container\ContainerInterface;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;

use User\Component\Test1\Administrator\Service\HTML\Icon;
/**
 * Component class for com_test1
 *
 * @since  __BUMP_VERSION__
 */
class Test1Component extends MVCComponent implements BootableExtensionInterface, CategoryServiceInterface, AssociationServiceInterface, RouterServiceInterface
{
	use HTMLRegistryAwareTrait;

    use CategoryServiceTrait;
    use AssociationServiceTrait;
	use RouterServiceTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('test1administrator', new AdministratorService);
        $this->getRegistry()->register('test1icon', new Icon($container->get(SiteApplication::class)));
	}

    /**
     * Adds Count Items for Category Manager.
     *
     * @param \stdClass[] $items The category objects
     * @param string $section The section
     *
     * @return  void
     *
     * @since   __BUMP_VERSION__
     */
    public
    function countItems(array $items, string $section)
    {
        try {
            $config = (object)[
                'related_tbl' => $this->getTableNameForSection($section),
                'state_col' => 'published',
                'group_col' => 'catid',
                'relation_type' => 'category_or_group',
            ];

            ContentHelper::countRelations($items, $config);
        } catch (\Exception $e) {
            // Ignore it
        }
    }

	/**
	 * Returns the table for the count items functions for the given section.
	 *
	 * @param   string  $section  The section
	 *
	 * @return  string|null
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getTableNameForSection(string $section = null)
	{
		return ($section === 'category' ? 'categories' : 'test1');
	}
	/**
	 * Returns the state column for the count items functions for the given section.
	 *
	 * @param   string  $section  The section
	 *
	 * @return  string|null
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getStateColumnForSection(string $section = null)
	{
		return 'published';
	}

}
