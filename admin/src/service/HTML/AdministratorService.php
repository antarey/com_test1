<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_test1
 *
 * @copyright   Copyright (C) 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace User\Component\Test1\Administrator\Service\HTML;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

/**
 * Test1 HTML class.
 *
 * @since  __BUMP_VERSION__
 */
class AdministratorService
{

    /**
     * Get the associated language flags
     *
     * @param integer $itemid The item id to search associations
     *
     * @return  string  The language HTML
     *
     * @throws  Exception
     */
    public function association($itemid)
    {
        // Defaults
        $html = '';
	    $jinput = Factory::getApplication()->input;
	    $view = $jinput->get('view');
        // Get the associations
        if ($associations = Associations::getAssociations('com_test1', '#__test1', 'com_test1.item', $itemid, 'id', null)) {
            foreach ($associations as $tag => $associated) {
                $associations[$tag] = (int)$associated->id;
            }

            // Get the associated test1 items
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('c.id, c.name as title')
                ->select('l.sef as lang_sef, lang_code')
                ->from('#__test1 as c')
                ->select('cat.title as category_title')
                ->join('LEFT', '#__categories as cat ON cat.id=c.catid')
                ->where('c.id IN (' . implode(',', array_values($associations)) . ')')
                ->where('c.id != ' . $itemid)
                ->join('LEFT', '#__languages as l ON c.language=l.lang_code')
                ->select('l.image')
                ->select('l.title as language_title');
            $db->setQuery($query);

            try {
                $items = $db->loadObjectList('id');
            } catch (\RuntimeException $e) {
                throw new \Exception($e->getMessage(), 500, $e);
            }

            if ($items) {
                foreach ($items as &$item) {
                    $text = strtoupper($item->lang_sef);
                    $url = Route::_('index.php?option=com_test1&task='.$view.'.edit&id=' . (int)$item->id);
                    $tooltip = '<strong>' . htmlspecialchars($item->language_title, ENT_QUOTES, 'UTF-8') . '</strong><br>'
                        . htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') . '<br>' . Text::sprintf('JCATEGORY_SPRINTF', $item->category_title);
                    $classes = 'badge bg-secondary';

                    $item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes . '">' . $text . '</a>'
                        . '<div role="tooltip" id="tip' . (int)$item->id . '">' . $tooltip . '</div>';
                }
            }

            $html = LayoutHelper::render('joomla.content.associations', $items);
        }

        return $html;
    }

}
