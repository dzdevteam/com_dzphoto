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
class DZPhotoHelper
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
    
    /**
     * Create a new item in database to represent a image
     *
     * @param array $links Contain 4 links (thumb, medium, large, original) to the image
     *
     * @return void
     */
    public static function createImageItem($links)
    {
        // We need at least the link to original image
        if ( !(is_array($links) && array_key_exists('original', $links)) ) {
            throw new Exception(JText::_('COM_DZPHOTO_INVALID_LINKS_ARRAY'), 500);
        }
        
        // Prepare necessary model and table
        $model = JModelLegacy::getInstance('Image', 'DZPhotoModel');
        $table = $model->getTable();
        $data = array(
            'id' => 0,
            'title' => pathinfo($links['original'], PATHINFO_BASENAME),
            'links' => $links
        );
        
        $user = JFactory::getUser();
        if (!$user->authorise('core.create', 'com_dzphoto.image')) {
            DZPhotoHelper::exitWithError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 500);
        }
        
        $form = $model->getForm($data, false);
        if (!$form) {
            DZPhotoHelper::exitWithError($model->getError(), 500);
        };
        
        $validData = $model->validate($form, $data);
        if ($validData === false) {
            // Get the validation messages.
            $errors = $model->getErrors();
            $messages = array();
            
            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $messages[] = $errors[$i]->getMessage();
                }
                else
                {
                    $messages[] = $errors[$i];
                }
            }
            
            DZPhotoHelper::exitWithError(join(',', $messages), 500);
        }
        
        if (!isset($validData['tags']))
        {
            $validData['tags'] = null;
        }
        
        // Attempt to save data
        if (!$model->save($validData))
        {
            DZPhotoHelper::exitWithError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 500);
        }
    }
    
    /**
     * Return error and response code then exit application
     *
     * @param string $error Error message
     * @param int $status_code HTTP status code
     *
     * @return void
     */
    public static function exitWithError($error, $status_code = 500)
    {
        echo $error;
        header($_SERVER['SERVER_PROTOCOL'] . " $status_code " . $error, true, $status_code);
        jexit();
    }
}
