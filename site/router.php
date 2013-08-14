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
    
    if (isset($query['task'])) {
        $segments[] = implode('/',explode('.',$query['task']));
        unset($query['task']);
    }
    if (isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
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
    
    // view is always the first element of the array
    $count = count($segments);
    
    if ($count)
    {
        $count--;
        $segment = array_pop($segments) ;
        if (is_numeric($segment)) {
            $vars['id'] = $segment;
        }
        else{
            $count--;
            $vars['task'] = array_pop($segments) . '.' . $segment;
        }
    }

    if ($count)
    {   
        $vars['task'] = implode('.',$segments);
    }
    return $vars;
}
