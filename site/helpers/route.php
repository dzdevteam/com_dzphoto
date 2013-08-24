<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * DZ Product Route Helper
 *
 * @static
 * @package     com_dzproduct
 */
abstract class DZPhotoHelperRoute
{
    protected static $lookup = null;

    /**
     * @param   integer  The route of the content item
     */
    public static function getImageRoute($id, $catid = 0, $language = 0)
    {
        //Create the link
        $link = 'index.php?option=com_dzphoto&view=image&id='. $id;

        if ($itemId = self::_findItemid(array('image', 'images')))
            $link .= '&Itemid='.$itemId;
        
        return $link;
    }
    
    protected  static function _findItemid($needle)
    {
        $app        = JFactory::getApplication();
        $menus      = $app->getMenu('site');
        
        // Prepare the reverse lookup array.
        if (self::$lookup === null)
        {
            self::$lookup = array();

            $component  = JComponentHelper::getComponent('com_dzphoto');
            $items      = $menus->getItems('component_id', $component->id);
            
            foreach ($items as $item)
            {
                if (isset($item->query) && isset($item->query['view']))
                {
                    $view = $item->query['view'];
                    self::$lookup[$view] = $item->id;
                }
            }
        }
        
        foreach ($needle as $view) {
            if (isset(self::$lookup[$view]))
                return self::$lookup[$view];
        }
            
        return null;
    }
}
