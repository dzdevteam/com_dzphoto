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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_dzphoto/assets/css/dzphoto.css');
$document->addStyleSheet('components/com_dzphoto/assets/css/dropzone.css');
$document->addScript('components/com_dzphoto/assets/js/dropzone.js');
$document->addScript('components/com_dzphoto/assets/js/dzphoto.js');
?>

<?php if(!empty($this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php else : ?>
<div id="j-main-container">
<?php endif;?>
    <?php echo JHtml::_('bootstrap.startAccordion', 'upload-arcordion', array('parent' => true, 'active' => 'new-album')); ?>
        <?php echo JHtml::_('bootstrap.addSlide', 'upload-arcordion', JText::_('COM_DZPHOTO_UPLOAD_NEW_ALBUM'), 'new-album'); ?>
        <form id="album-form" class="form-horizontal" action="index.php?option=com_dzphoto&amp;task=upload.newalbum&amp;format=json">
            <div class="control-group">
                <?php echo $this->form->getLabel('newalbum'); ?>
                <div class="controls">
                    <?php echo $this->form->getInput('newalbum'); ?>
                    <button id="newalbum-submit" class="btn btn-primary"><?php echo JText::_('COM_DZPHOTO_UPLOAD_CREATE_ALBUM'); ?></button>
                </div>
            </div>
            <div class="control-group">
                <?php echo $this->form->getLabel('albums'); ?>
                <div class="controls">
                    <?php echo $this->form->getInput('albums'); ?>
                </div>
            </div>
            <?php echo JHtml::_('form.token'); ?>
        </form>
        <button id="proceed" class="btn btn-primary"><?php echo JText::_('COM_DZPHOTO_UPLOAD_PROCEED'); ?></button>
        <?php echo JHtml::_('bootstrap.endSlide'); ?>
        <?php echo JHtml::_('bootstrap.addSlide', 'upload-arcordion', JText::_('COM_DZPHOTO_UPLOAD_ADD_IMAGES'), 'add-images'); ?>
        <form action="<?php echo JRoute::_('index.php?option=com_dzphoto&view=upload&task=upload.upload'); ?>" method="post" id="adminForm" enctype="multipart/form-data" class="dropzone dz-clickable">
            <div class="fallback">
                <?php echo JText::_('COM_DZPHOTO_UPLOAD_ERROR_BROWSER_NOT_SUPPORTED'); ?>
            </div>
            <input id="albumid" type="hidden" name="album" value="" />
            <?php echo JHtml::_( 'form.token' ); ?>
        </form>
        <div class="row" id="clearzone" style="display:none;">
            <div class="span12 text-center">
                <button class="btn btn-danger"><?php echo JText::_('COM_DZPHOTO_UPLOAD_CLEAR_ALL'); ?></button>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endSlide'); ?>
    <?php echo JHtml::_('bootstrap.endAccordion'); ?>
</div>
