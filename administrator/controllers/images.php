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
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'image', $prefix = 'DzphotoModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
    
    
    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        // Get the input
        $input = JFactory::getApplication()->input;
        $pks = $input->post->get('cid', array(), 'array');
        $order = $input->post->get('order', array(), 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }
    
    /**
     * Method to save the image title and caption through ajax
     *
     * @return void
     */
    public function saveImageAjax()
    {
        header('Content-Type: application/json');
        JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
        
        // Get the input
        $input = JFactory::getApplication()->input;
        $data = $input->post->get('jform', array(), 'array');
        
        DZPhotoHelper::updateImageItem($data);
        echo json_encode(array('message' => JText::_('COM_DZPHOTO_SAVE_SUCCESS')));
        JFactory::getApplication()->close();
    }
}