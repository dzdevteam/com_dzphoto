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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/dzphoto.php';

/**
 * Upload controller class.
 */
class DZPhotoControllerUpload extends JControllerLegacy
{

    function __construct() {
        parent::__construct();
    }

    public function upload() {
        if (!empty($_FILES)) {
            $app = JFactory::getApplication();
            $params = JComponentHelper::getParams('com_dzphoto');
            
            // Get current year and month
            $date = JFactory::getDate();
            $year = $date->format('Y');
            $month = $date->format('m');
            
            // Get root location for storing images
            $basedir = $params->get('imagedir', 'images/dzphoto');
            
            // Create a location for this image
            $dest = JPATH_ROOT.'/'.$basedir.'/'.$year.'/'.$month;
            if (!JFolder::exists($dest)) {
                $result = JFolder::create($dest);
                if (!$result) {
                    throw new Exception(JText::_('COM_DZPHOTO_ERROR_CREATE_DIRECTORY'), 500);
                }
            }
            
            // Prepare file names
            $tmpfile = $_FILES['file']['tmp_name'];            
            $name = JFile::makeSafe(sprintf('%u', crc32($_FILES['file']['name'].time())).'-'.$_FILES['file']['name']);
            $targetfile = $dest.'/'.$name;
            
            // Now upload the image
            $result = JFile::upload($tmpfile, $targetfile);
            if (!$result) {
                throw new Exception(JText::_('COM_DZPHOTO_ERROR_MOVE_FILE'), 500);
            }
            
            // Now we create different sizes for this image
            $links = DZPhotoHelper::generateThumbs($targetfile);
            
            // Announce successfully uploaded files
            $this->setMessage(JText::_('COM_DZPHOTO_UPLOAD_SUCCESS'));
        }
        // Redirect back to view upload
        $this->setRedirect('index.php?option=com_dzphoto&view=upload');
        $this->redirect();
    }
    
    /**
     * Attach dimension information into current image name
     * @param string $name
     * @param int $width
     * @param int $height
     *
     * @return string $newName
     */
    private function _attachDimension($name, $width, $height)
    {
        // Get file extension and name
        $info = pathinfo($name);
        $filename = basename($name, '.'.$info['extension']);
        
        return $filename.'_'.$width.'x'.$height.'.'.$info['extension'];
    }
}