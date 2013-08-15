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

JHtml::_('jquery.framework');

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
    <form action="<?php echo JRoute::_('index.php?option=com_dzphoto&view=upload&task=upload.upload'); ?>" method="post" id="adminForm" enctype="multipart/form-data" class="dropzone dz-clickable">
        <div class="fallback">
            <input type="file" name="file" accept="image/*" />
            <input type="submit" class="btn" value="<?php echo JText::_('JSUBMIT'); ?>" />
        </div>
        <?php echo JHtml::_( 'form.token' ); ?>
    </form>
    <div class="row" id="clearzone" style="display:none;">
        <div class="span12 text-center">
            <button class="btn btn-danger"><?php echo JText::_('COM_DZFOODMENU_CLEAR_ALL'); ?></button>
        </div>
    </div>
</div>
