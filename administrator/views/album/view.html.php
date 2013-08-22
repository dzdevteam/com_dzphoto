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
 * View class for a list of Dzphoto.
 */
class DzphotoViewAlbum extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->state        = $this->get('State');
        $this->items        = $this->get('Items');
        $this->pagination   = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        
        $this->addToolbar();
        
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT.'/helpers/dzphoto.php';
        
        $this->extra_sidebar = '';
    }
    
    protected function getSortFields()
    {
        return array(
        'a.id' => JText::_('JGRID_HEADING_ID'),
        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
        'a.state' => JText::_('JSTATUS'),
        'a.title' => JText::_('COM_DZPHOTO_IMAGES_TITLE'),
        'a.created' => JText::_('COM_DZPHOTO_IMAGES_CREATED')
        );
    }
}
