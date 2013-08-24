<?php
/**
 * @version     1.0.0
 * @package     com_dzphoto
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      DZ Team <dev@dezign.vn> - dezign.vn
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @param   array   A named array
 * @return  array
 */
function DzphotoBuildRoute(&$query)
{
    $segments = array();
    
    // get a menu item based on Itemid or currently active
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    $coupled = false; // Indicate couple views

    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if (empty($query['Itemid']))
    {
        $menuItem = $menu->getActive();
        $menuItemGiven = false;
    }
    else
    {
        $menuItem = $menu->getItem($query['Itemid']);
        $menuItemGiven = true;
    }

    // check again
    if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_dzphoto')
    {
        $menuItemGiven = false;
        unset($query['Itemid']);
    }

    if (isset($query['view']))
    {
        $view = $query['view'];
    }
    else
    {
        // we need to have a view in the query or it is an invalid URL
        return $segments;
    }
    
    if ($menuItem instanceof stdClass) { 
        // are we dealing with an item that is attached to a menu item?
        if($menuItem->query['view'] == $query['view'] && isset($query['id']) && $menuItem->query['id'] == (int) $query['id'])
        {
            unset($query['view']);

            if (isset($query['layout']))
            {
                unset($query['layout']);
            }

            unset($query['id']);

            return $segments;
        }
        
        // We can imply a single view from its list view
        // Thus we can remove view from the query in some cases
        if ( $menuItem->query['view'] == 'images' && $query['view'] == 'image' ) {
            unset($query['view']);
            $coupled = true;
        }
    }
    

    if ($view == 'image')
    {
        if (!$menuItemGiven || !$coupled)
        {
            $segments[] = $view;
            if (isset($query['view']))
                unset($query['view']);
        }

        
        if (isset($query['id']))
        {
            // Make sure we have the id and the alias
            if (strpos($query['id'], ':') === false)
            {
                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('alias')
                    ->where('id=' . (int) $query['id'])
                    ->from('#__dzphoto_images');
                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                $query['id'] = $query['id'] . ':' . $alias;
            }
        }
        else
        {
            // we should have id set for this view.  If we don't, it is an error
            return $segments;
        }
        
        $segments[] = $query['id'];
        
        unset($query['id']);
    }

    // if the layout is specified and it is the same as the layout in the menu item, we
    // unset it so it doesn't go into the query string.
    if (isset($query['layout']))
    {
        if ($menuItemGiven && isset($menuItem->query['layout']))
        {
            if ($query['layout'] == $menuItem->query['layout'])
            {
                unset($query['layout']);
            }
        }
        else
        {
            if ($query['layout'] == 'default')
            {
                unset($query['layout']);
            }
        }
    }

    return $segments;
}

/**
 * @param   array   A named array
 * @param   array
 *
 * Formats:
 *
 * index.php?/dzphoto/task/id/Itemid
 *
 * index.php?/dzphoto/id/Itemid
 */
function DzphotoParseRoute($segments)
{
    $vars = array();
    
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    $menuItem = $menu->getActive();
    
    // view is always the first element of the array
    $count = count($segments);
    
    if ($count >= 2) {
        $vars['id'] = (int) $segments[$count-1];
        $vars['view'] = $segments[$count-2];
    } elseif ($count == 1) {
        if ($menuItem) {
            switch ($menuItem->query['view']) {
                case 'images':
                    $vars['view'] = 'image';
                    break;
                default:
                    break;
            }
            $vars['id'] = (int) $segments[0];
        } else {
            $vars['view'] = $segments[0];
        }
    }
    return $vars;
}
