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
 * Dzphoto helper.
 */
class DzphotoHelper
{
    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_DZPHOTO_TITLE_UPLOAD'),
            'index.php?option=com_dzphoto&view=upload',
            $vName == 'upload'
        );
        
        JHtmlSidebar::addEntry(
            JText::_('COM_DZPHOTO_TITLE_IMAGES'),
            'index.php?option=com_dzphoto&view=images',
            $vName == 'images'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return  JObject
     * @since   1.6
     */
    public static function getActions()
    {
        $user   = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_dzphoto';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
    
    /**
     * Generate thumbnails and return links
     * @param string $imgfile URI of the image
     * @return array() array of 'medium', 'large' and 'thumb' links;
     */
    public static function generateThumbs($imgfile)
    {
        $params = JComponentHelper::getParams('com_dzphoto');
        $basedir = $params->get('imagedir', 'images/dzphoto');
        
        // Get different pre-configured sizes
        $thumbwidth = $params->get('thumbwidth', '150');
        $thumbheight = $params->get('thumbheight', '150');
        $mediumwidth = $params->get('mediumwidth', '300');
        $mediumheight = $params->get('mediumheight', '300');
        $largewidth = $params->get('largewidth', '1000');
        $largeheight = $params->get('largeheight', '1000');
        
        // Generate thumbs
        $image = new JImage($imgfile);
        $imagedir = pathinfo($image->getPath(), PATHINFO_DIRNAME);
        $other_thumbs = $image->createThumbs(array($mediumwidth.'x'.$mediumheight, $largewidth.'x'.$largeheight), JImage::SCALE_INSIDE, $imagedir);
        $thumb = $image->createThumbs($thumbwidth.'x'.$thumbheight, JImage::CROP_RESIZE, $imagedir); // Bug: CROP_RESIZE will change the handle in JImage object
        
        $links = array();
        $basepath = substr($imagedir, strpos($imagedir, $basedir));
        $links['thumb'] = $basepath. '/' . pathinfo($thumb[0]->getPath(), PATHINFO_BASENAME);
        $links['medium'] = $basepath. '/' . pathinfo($other_thumbs[0]->getPath(), PATHINFO_BASENAME);
        $links['large'] = $basepath. '/' . pathinfo($other_thumbs[1]->getPath(), PATHINFO_BASENAME);
        
        return $links;
    }
}
