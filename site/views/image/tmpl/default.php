<?php
/**
 * @version     1.0.0
 * @package     com_dzphoto
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      DZ Team <dev@dezign.vn> - dezign.vn
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_dzphoto', JPATH_ADMINISTRATOR);

?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_ID'); ?>:
            <?php echo $this->item->id; ?></li>
            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_ORDERING'); ?>:
            <?php echo $this->item->ordering; ?></li>
            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_STATE'); ?>:
            <?php echo $this->item->state; ?></li>
            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_CREATED_BY'); ?>:
            <?php echo $this->item->created_by; ?></li>
            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_TITLE'); ?>:
            <?php echo $this->item->title; ?></li>
            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_ALIAS'); ?>:
            <?php echo $this->item->alias; ?></li>
            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_CAPTION'); ?>:
            <?php echo $this->item->caption; ?></li>
            <li><?php echo JText::_('COM_DZPHOTO_FORM_LBL_IMAGE_LINK'); ?>:
                <ul>
                    <?php foreach ($this->item->links as $type => $link) : ?>
                    <li><?php echo JText::_('COM_DZPHOTO_IMAGES_LINK_'.$type); ?>: <?php echo $link; ?>
                    <?php endforeach; ?>
                </ul>
            </li>


        </ul>

    </div>
    
<?php
else:
    echo JText::_('COM_DZPHOTO_ITEM_NOT_LOADED');
endif;
?>
