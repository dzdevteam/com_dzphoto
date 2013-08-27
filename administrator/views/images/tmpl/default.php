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
JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.modal');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$selector = '#jform_tags';
JHtml::_('formbehavior.ajaxchosen', 
    new JRegistry(
        array(
            'selector' => $selector, 
            'url'      => JUri::root() . 'index.php?option=com_tags&task=tags.searchAjax',
            'dataType'    => 'json',
            'jsonTermKey' => 'like'
        )
    )
);
JFactory::getDocument()->addScriptDeclaration("
    (function($){
        $(document).ready(function () {

            var customTagPrefix = '#new#';

            // Method to add tags pressing enter
            $('" . $selector . "_chzn input').keydown(function(event) {

                // Tag is greater than 3 chars and enter pressed
                if (this.value.length >= 3 && (event.which === 13 || event.which === 188)) {

                    // Search an highlighted result
                    var highlighted = $('" . $selector . "_chzn').find('li.active-result.highlighted').first();

                    // Add the highlighted option
                    if (event.which === 13 && highlighted.text() !== '')
                    {
                        // Extra check. If we have added a custom tag with this text remove it
                        var customOptionValue = customTagPrefix + highlighted.text();
                        $('" . $selector . " option').filter(function () { return $(this).val() == customOptionValue; }).remove();

                        // Select the highlighted result
                        var tagOption = $('" . $selector . " option').filter(function () { return $(this).html() == highlighted.text(); });
                        tagOption.attr('selected', 'selected');
                    }
                    // Add the custom tag option
                    else
                    {
                        var customTag = this.value;

                        // Extra check. Search if the custom tag already exists (typed faster than AJAX ready)
                        var tagOption = $('" . $selector . " option').filter(function () { return $(this).html() == customTag; });
                        if (tagOption.text() !== '')
                        {
                            tagOption.attr('selected', 'selected');
                        }
                        else
                        {
                            var option = $('<option>');
                            option.text(this.value).val(customTagPrefix + this.value);
                            option.attr('selected','selected');

                            // Append the option an repopulate the chosen field
                            $('" . $selector . "').append(option);
                        }
                    }

                    this.value = '';
                    $('" . $selector . "').trigger('liszt:updated');
                    event.preventDefault();

                }
            });
        });
    })(jQuery);
    "
);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_dzphoto/assets/css/jquery.Jcrop.min.css');
$document->addScript('components/com_dzphoto/assets/js/jquery.Jcrop.min.js');
$document->addStyleSheet('components/com_dzphoto/assets/css/dzphoto.css');
$document->addScript('components/com_dzphoto/assets/js/dzphoto.js');

$user   = JFactory::getUser();
$userId = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_dzphoto');
$saveOrder  = $listOrder == 'a.ordering';
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_dzphoto&task=images.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'imageList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
    Joomla.orderTable = function() {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_dzphoto&view=images'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
    
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER');?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                    <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                    <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                </select>
            </div>
        </div>        
        <div class="clearfix"> </div>
        <table class="table table-striped" id="imageList">
            <thead>
                <tr>
                <?php if (isset($this->items[0]->ordering)): ?>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>
                <?php endif; ?>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                <?php if (isset($this->items[0]->state)): ?>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>
                <th class='left' width="20%">
                <?php echo JHtml::_('grid.sort',  'COM_DZPHOTO_IMAGES_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th class='left' width="20%">
                <?php echo JText::_('COM_DZPHOTO_IMAGES_CAPTION'); ?>
                </th>
                <th class='center' width="15%">
                <?php echo JText::_('COM_DZPHOTO_IMAGES_PREVIEW_EDIT'); ?>
                </th>
                <th class='left'>
                <?php echo JText::_('COM_DZPHOTO_IMAGES_TAGS'); ?>
                </th>
                <th class='center'>
                <?php echo JText::_('COM_DZPHOTO_IMAGES_LINK'); ?>
                </th>
                    
                <th class='left'>
                    <?php echo JHtml::_('grid.sort',  'COM_DZPHOTO_IMAGES_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
                </th>
                <?php if (isset($this->items[0]->id)): ?>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>
                </tr>
            </thead>
            <tfoot>
                <?php 
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
                    $colspan = 10;
                }
            ?>
            <tr>
                <td colspan="<?php echo $colspan ?>">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <?php foreach ($this->items as $i => $item) :
                $ordering   = ($listOrder == 'a.ordering');
                $canCreate  = $user->authorise('core.create',       'com_dzphoto');
                $canEdit    = $user->authorise('core.edit',         'com_dzphoto');
                $canCheckin = $user->authorise('core.manage',       'com_dzphoto');
                $canChange  = $user->authorise('core.edit.state',   'com_dzphoto');
                ?>
                <tr class="row<?php echo $i % 2; ?>" id="row-item-<?php echo $item->id; ?>">
                    
                <?php if (isset($this->items[0]->ordering)): ?>
                    <td class="order nowrap center hidden-phone">
                    <?php if ($canChange) :
                        $disableClassName = '';
                        $disabledLabel    = '';
                        if (!$saveOrder) :
                            $disabledLabel    = JText::_('JORDERINGDISABLED');
                            $disableClassName = 'inactive tip-top';
                        endif; ?>
                        <span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
                            <i class="icon-menu"></i>
                        </span>
                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                    <?php else : ?>
                        <span class="sortable-handler inactive" >
                            <i class="icon-menu"></i>
                        </span>
                    <?php endif; ?>
                    </td>
                <?php endif; ?>
                    <td class="center hidden-phone">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                <?php if (isset($this->items[0]->state)): ?>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->state, $i, 'images.', $canChange, 'cb'); ?>
                    </td>
                <?php endif; ?>
                <td contenteditable="true" tabindex="1" data-field="title" data-id="<?php echo $item->id; ?>">
                    <?php echo $item->title; ?>
                </td>
                <td contenteditable="true" tabindex="1" data-field="caption" data-id="<?php echo $item->id; ?>">
                    <?php echo $item->caption; ?>
                </td>
                <td>
                    <a 
                        href="<?php echo JUri::root().$item->links['large']; ?>" 
                        title="<?php echo JText::_('COM_DZPHOTO_IMAGES_LARGE_SIZE'); ?>" 
                        data-original="<?php echo JUri::root().$item->links['original']; ?>"
                        data-image-id="<?php echo $item->id; ?>"
                        class="img-modal">
                        <img src="<?php echo JUri::root().$item->links['thumb']; ?>" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>" />
                    </a>
                </td>
                <td>
                    <?php $tags = array(); ?>
                    <?php foreach($item->tags->itemTags as $tag) { $tags[$tag->id] = $tag->title; } ?>
                    <?php echo join(', ', $tags); ?>
                    <br />
                    <a href="#" class="tags-modal" 
                        data-item-id="<?php echo $item->id; ?>" 
                        data-item-tags='<?php echo json_encode($tags, JSON_FORCE_OBJECT); ?>' 
                        data-item-title="<?php echo $item->title; ?>">
                        <span class="icon-pencil" aria-hidden="true"></span>&nbsp;<?php echo JText::_('COM_DZPHOTO_IMAGES_EDIT_TAGS'); ?>
                    </a>
                </td>
                <td class="center">
                    <a href="<?php echo JURI::root().$item->links['original']; ?>" target="_nblank" class="btn btn-link">
                        <?php echo JText::_('COM_DZPHOTO_IMAGES_LINK_ORIGINAL'); ?>&nbsp;<span class="icon-out-2" aria-hidden="true"></span>
                    </a><br />
                    <a href="<?php echo JURI::root().$item->links['thumb']; ?>" target="_nblank" class="btn btn-link">
                        <?php echo JText::_('COM_DZPHOTO_IMAGES_LINK_THUMB'); ?>&nbsp;<span class="icon-out-2" aria-hidden="true"></span>
                    </a><br />
                    <a href="<?php echo JURI::root().$item->links['medium']; ?>" target="_nblank" class="btn btn-link">
                        <?php echo JText::_('COM_DZPHOTO_IMAGES_LINK_MEDIUM'); ?>&nbsp;<span class="icon-out-2" aria-hidden="true"></span>
                    </a><br />
                    <a href="<?php echo JURI::root().$item->links['large']; ?>" target="_nblank" class="btn btn-link">
                        <?php echo JText::_('COM_DZPHOTO_IMAGES_LINK_LARGE'); ?>&nbsp;<span class="icon-out-2" aria-hidden="true"></span>
                    </a>
                </td>

                <td>
                    <?php echo $item->created_by; ?>
                </td>
                <?php if (isset($this->items[0]->id)): ?>
                    <td class="center hidden-phone">
                        <?php echo (int) $item->id; ?>
                    </td>
                <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>        
<div id="item-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-header">
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <button class="btn close-btn" data-dismiss="modal" aria-hidden="true">
            <span class="icon-cancel small" aria-hidden="true"></span>&nbsp;<?php echo JText::_('JTOOLBAR_CLOSE'); ?>
        </button>
        <button class="btn btn-primary submit-btn">
            <?php echo JText::_('JSUBMIT'); ?>
        </button>
    </div>
</div>
<div id="hidden-area" style="display:none">
    <div class="tags-container">
        <form id="tags_form" action="index.php?option=com_dzphoto&amp;task=images.saveImageAjax&amp;format=json" method="POST">
            <input id="jform_id" type="hidden" name="jform[id]" />
            <select id="jform_tags" name="jform[tags][]" multiple="true">
            </select>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
