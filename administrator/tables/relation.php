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
 * image Table class
 */
class DzphotoTableRelation extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__dzphoto_relations', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param   array       Named array
     * @return  null|string null is operation was satisfactory, otherwise returns an error
     * @see     JTable:bind
     * @since   1.5
     */
    public function bind($array, $ignore = '') {
        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check function
     */
    public function check() {
        // Verify that the relation is unique
        $table = JTable::getInstance('Relation', 'DZPhotoTable');
        if ($table->load(array('catid' => $this->catid, 'imageid' => $this->imageid)) && ($table->id != $this->id || $this->id == 0))
        {
            $this->setError(JText::_('COM_DZPHOTO_ERROR_RELATION_ALREADY_ESTABLISHED'));
            return false;
        }
        return parent::check();
    }

    /**
     * Overrides JTable::store to set created time and user id
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     */
    public function store($updateNulls = false)
    {        
        return parent::store($updateNulls);
    }
}
