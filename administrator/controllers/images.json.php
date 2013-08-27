<?php
/**
 * @version     1.0.0
 * @package     com_dzphoto
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      DZ Team <dev@dezign.vn> - dezign.vn
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/dzphoto.php';

/**
 * Images list controller class.
 */
class DzphotoControllerImages extends JControllerAdmin
{
    public function __construct($config = array()) {
        parent::__construct();
        header('Content-Type: application/json');
    }
    /**
     * Method to save the image title and caption through ajax
     *
     * @return void
     */
    public function saveImageAjax()
    {
        JSession::checkToken('request') or DZPhotoHelper::exitWithError(JText::_('JINVALID_TOKEN'));
        
        // Get the input
        $data = $this->input->post->get('jform', array(), 'array');
        
        DZPhotoHelper::updateImageItem($data);
        echo json_encode(array('status' => 'ok', 'message' => JText::_('COM_DZPHOTO_SAVE_SUCCESS')));
        jexit();
    }
    
    /**
     * Method to scale the image
     *
     * @return void
     */
    public function cropImageAjax()
    {
        // Get the selection
        $selection = $this->input->post->get('selection', array(), 'array');
        
        try {
            $newlinks = DZPhotoHelper::cropImage($selection['id'], $selection['w'], $selection['h'], $selection['x'], $selection['y']);
        } catch (Exception $e) {
            DZPhotoHelper::exitWithError($e->getMessage());
        }
        
        foreach ($newlinks as &$link) {
            $link = JUri::root().$link;
        }
        echo json_encode(array('status' => 'ok', 'message' => JText::_('COM_DZPHOTO_CROP_SUCCESS'), 'links' => $newlinks));
        jexit();
    }
}