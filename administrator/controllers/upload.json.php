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
        header('Content-Type: application/json');
    }

    public function upload() {
        JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
        
        $file = $this->input->files->get('file', '', 'array');
        if (!empty($file)) {
            try {
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
                        throw new RuntimeException(JText::_('COM_DZPHOTO_ERROR_CREATE_DIRECTORY')); 
                    }
                }
                
                // Prepare file names
                $tmpfile = $file['tmp_name'];            
                $name = JFile::makeSafe(sprintf('%u', crc32($file['name'].time())).'-'.$file['name']);
                $targetfile = $dest.'/'.$name;
                
                // Now upload the image
                $result = JFile::upload($tmpfile, $targetfile);
                if (!$result) {
                    throw new RuntimeException(JText::_('COM_DZPHOTO_ERROR_MOVE_FILE'));
                }
                
                // Now we create different sizes for this image
                $links = DZPhotoHelper::generateThumbs($targetfile);
                
                // Create a new item in database to represent the image
                $album = $this->input->get('album', 0, 'int');
                $links['original'] = $basedir.'/'.$year.'/'.$month.'/'.$name;
                DZPhotoHelper::updateImageItem(
                    array(
                        'id' => 0, 
                        'title' => pathinfo($links['original'], PATHINFO_BASENAME),
                        'alias' => pathinfo($links['original'], PATHINFO_FILENAME),
                        'links' => $links,
                        'album' => $album
                    )
                );
                
                // Return JSON object containing the links
                echo json_encode($links);
            } catch (Exception $e) {
                DZPhotoHelper::catchException($e);
            }
        }
        
        jexit(); // Close application
    }
    
    public function newalbum() {
        JSession::checkToken('request') or jexit(json_encode(array('status' => 'nok', 'message' => JText::_('JINVALID_TOKEN'))));
        
        try {
            $newname = $this->input->post->get('newalbum', '', 'string');
            if (empty($newname)) {
                throw new InvalidArgumentException(JText::_('COM_DZPHOTO_WARNING_PROVIDE_VALID_NAME'));
            }
            
            JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_categories/models');
            JModelLegacy::addTablePath(JPATH_ADMINISTRATOR.'/components/com_categories/tables');
            JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/com_categories/models/forms');
            $model = JModelLegacy::getInstance('Category', 'CategoriesModel');
            
            $user = JFactory::getUser();
            if (!$user->authorise('core.create', 'com_categories')) {
                throw new RuntimeException(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
            }
            
            $data = array(
                'id'        => 0, 
                'parent_id' => 1, 
                'title'     => $newname, 
                'extension' => 'com_dzphoto.images', 
                'published' => 1,
                'language'  => '*',
                'params'    => array()
            );
            $form = $model->getForm($data, false);
            if (!$form) {
                throw new RuntimeException($model->getError());
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
                
                throw new RuntimeException(join(',', $messages), 500);
            }
            
            if (!isset($validData['tags']))
            {
                if ($validData['id']) {
                    $tags = new JHelperTags();
                    $validData['tags'] = explode(',', $tags->getTagIds($validData['id'], 'com_dzphoto.image'));
                } else {
                    $validData['tags'] = null;
                }
            }
            // Attempt to save data
            if (!$model->save($validData))
            {
                throw new RuntimeException(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
            }
            
            // Annout success
            echo json_encode(array('status' => 'ok', 'message' => JText::_('COM_DZPHOTO_SAVE_SUCCESS')));
            jexit();
        } catch (Exception $e) {
            DZPhotoHelper::catchException($e);
        }
    }
}