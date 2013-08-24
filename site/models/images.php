<?php

/**
 * @version     1.0.0
 * @package     com_dzphoto
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      DZ Team <dev@dezign.vn> - dezign.vn
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_SITE.'/components/com_dzphoto/helpers/route.php';


/**
 * Methods supporting a list of Dzphoto records.
 */
class DzphotoModelImages extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'created_by', 'a.created_by',
                'created', 'a.created',
                'title', 'a.title',
                'alias', 'a.alias',
                'caption', 'a.caption',
                'link', 'a.link',

            );
        }
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState($ordering = 'created', $direction = 'DESC') {

        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);

        
        $orderCol = $app->input->get('filter_order', 'a.created');
        if (!in_array($orderCol, $this->filter_fields))
        {
            $orderCol = 'a.created';
        }

        $listOrder = $app->input->get('filter_order_Dir', 'DESC');
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
        {
            $listOrder = 'DESC';
        }
        
        // Only view published images
        $this->setState('filter.published', 1);
        
        // Filter by albums
        $albumid = $app->input->get('filter_albumid', array(), 'array');
        if (!empty($albumid)) {
            $this->setState('filter.albumid', $albumid);
        }
        
        // List state information.
        parent::populateState($orderCol, $listOrder);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );

        $query->from('`#__dzphoto_images` AS a');

        
        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
    
        // Join over the created by field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
        

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                
            }
        }

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('state = ' . (int) $published);
        }
        elseif (is_array($published))
        {
            JArrayHelper::toInteger($published);
            $published = implode(',', $published);
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('state IN (' . $published . ')');
        }
        
        // Filter by album
        $albumid = $this->getState('filter.albumid');
        if (is_numeric($albumid)) {
            $query->join('INNER', '#__dzphoto_relations as r ON r.imageid = a.id AND r.catid = ' .(int) $albumid);
        } elseif (is_array($albumid)) {
            JArrayHelper::toInteger($albumid);
            $albumid = implode(',', $albumid);
            $query->join('INNER', '#__dzphoto_relations as r ON r.imageid = a.id AND r.catid IN (' . $albumid . ')');
        }
        
        // Add the list ordering clause.
        $query->order($this->getState('list.ordering', 'a.created') . ' ' . $this->getState('list.direction', 'DESC'));
        
        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        foreach ($items as &$item) {
            // Convert links string to array of links
            $registry = new JRegistry();
            $registry->loadString($item->links);
            $item->links = $registry->toArray();
            
            // Pre-build link for item
            $item->link = DZPhotoHelperRoute::getImageRoute($item->id);
        }
        
        return $items;
    }

}
