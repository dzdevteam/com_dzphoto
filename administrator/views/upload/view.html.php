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

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class DZPhotoViewUpload extends JViewLegacy
{
    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseWarning(500, implode("\n", $errors));

            return false;
        }
        
        $this->addToolbar();
        
        DZPhotoHelper::addSubmenu('upload');
        
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_DZPHOTO_TITLE_UPLOAD'), 'image.png');
        
        $canDo = DZPhotoHelper::getActions();
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_dzphoto');
        }
    }
}
