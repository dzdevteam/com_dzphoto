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
class DZPhotoControllerAlbum extends JControllerLegacy
{

    function __construct() {
        parent::__construct();
        
        // Set default header for this controller
        header('Content-Type: application/json');
    }

    public function removeImage() {
        JSession::checkToken('request') or jexit(json_encode(array('status' => 'nok', 'message' => JText::_('JINVALID_TOKEN'))));
        
        // Get album id and image id
        $albumid = $this->input->get('albumid', 0, 'int');
        $imageid = $this->input->get('imageid', 0, 'int');
        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $conditions = array( 'catid' => $albumid, 'imageid' => $imageid );
        $query->delete('#__dzphoto_relations');
        $query->where('catid = ' . $albumid . ' AND imageid = ' . $imageid);
        
        $db->setQuery($query);
        
        try {
            $result = $db->execute();
        } catch (Exception $e) {
            DZPhotoHelper::exitWithError($e->getMessage(), 500);
        }
        
        echo json_encode(array('status' => 'ok', 'message' => JText::_('COM_DZPHOTO_REMOVE_SUCCESS')));
        jexit();
    }
    
    public function addImage() {
        JSession::checkToken('request') or jexit(json_encode(array('status' => 'nok', 'message' => JText::_('JINVALID_TOKEN'))));
        
        // Get album id and image id
        $albumid = $this->input->get('albumid', 0, 'int');
        $imageid = $this->input->get('imageid', 0, 'int');
        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $relation = new stdClass();
        $relation->catid = $albumid;
        $relation->imageid = $imageid;
        
        try {
            $db->insertObject('#__dzphoto_relations', $relation);
        } catch(Exception $e) {
            DZPhotoHelper::exitWithError($e->getMessage(), 500);
        }
        
        echo json_encode(array('status' => 'ok', 'message' => JText::_('COM_DZPHOTO_ADD_SUCCESS')));
        jexit();
    }
}