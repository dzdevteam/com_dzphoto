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
    }
}