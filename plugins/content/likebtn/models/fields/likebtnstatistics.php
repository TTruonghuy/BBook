<?php

/**
 * @project  LikeBtn Like Button
 * @author   LikeBtn.com (info@likebtn.com)
 * @copyright (Copyright (C) 2013 by LikeBtn. All rights reserved.
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

require_once(dirname(__FILE__).'/../../likebtn.class.php');

/**
 * Provides LikeBtn settings for content types
 *
 * @package     Joomla.Plugin
 * @subpackage  Likebtn
 * @since       2.5.5
 */
class JFormFieldLikebtnstatistics extends JFormField {

    // The field class must know its own type through the variable $type.
    protected $type = 'Likebtnstatistics';

    public function getLabel() {

        // Run sunchronization
        $likebtn = new LikeBtn(plgContentLikebtn::getPluginParams());
        $likebtn->runSyncVotes();

        // Get a db connection.
        $db = JFactory::getDbo();

        $content_types = plgContentLikebtn::getSupportedContentTypes($db);

        $custom_item = new stdClass();
        $custom_item->type_title = JText::_('PLG_LIKEBTN_CONTENT_TYPE_CUSTOM');
        $custom_item->type_alias = LIKEBTN_CONTENT_TYPE_CUSTOM_ITEM;

        $content_types[] = $custom_item;

        ?>
        <a href="javascript:toggleStatToUpgrade();void(0);"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TO_ENABLE'); ?>...</a>
        <br/>
        <ol id="likebtn_stat_to_upgrade" style="display: none;">
            <?php echo JText::_('PLG_LIKEBTN_STATISTICS_UPGRADE'); ?>
        </ol>
        <br/>

        <div class="control-group">
            <div class="control-label"><label><?php echo JText::_('PLG_LIKEBTN_STATISTICS_CONTENT_TYPE'); ?></label></div>
            <div class="controls">
                <select id="likebtn_statistics_param_content_type" name="likebtn_statistics_param_content_type">
                    <?php foreach ($content_types as $content_type): ?>
                        <option <?php if ($content_type->type_alias == 'com_content.article'): ?>selected="selected"<?php endif ?> value="<?php echo $content_type->type_alias; ?>"><?php echo $content_type->type_title; ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label"><label><?php echo JText::_('PLG_LIKEBTN_STATISTICS_ORDER_BY'); ?></label></div>
            <div class="controls">
                <select id="likebtn_statistics_param_order_by" name="likebtn_statistics_param_order_by">
                    <option value="likes"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_LIKES'); ?></option>
                    <option value="dislikes"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_DISLIKES'); ?></option>
                    <option value="likes_minus_dislikes"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_LIKES_MINUS_DISLIKES'); ?></option>
                </select>
            </div>
        </div>

        <?php /*&nbsp;&nbsp;
        <?php echo JText::_('PLG_LIKEBTN_STATISTICS_PAGE_SIZE'); ?>:
        <select id="likebtn_statistics_param_page_size" name="likebtn_statistics_param_page_size">
            <option value="10">10</option>
            <option value="20">20</option>
            <option selected="selected" value="50">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
            <option value="1000">1000</option>
            <option value="5000">5000</option>
        </select>*/ ?>

        <div class="statistics_filter_container well form-inline">
            <div class="control-group" style="margin-bottom: 0">
                <div class="control-label"><label><?php echo JText::_('PLG_LIKEBTN_STATISTICS_FILTER_ID'); ?></label></div>
                <div class="controls">
                    <input type="text" size="8" value="" id="likebtn_statistics_param_id" name="likebtn_statistics_param_id">
                    <?php /*&nbsp;&nbsp;
                    <?php echo JText::_('PLG_LIKEBTN_STATISTICS_FILTER_TITLE'); ?>:
                    <input type="text" size="60" value="" id="likebtn_statistics_param_content_title" name="likebtn_statistics_param_content_title">*/ ?>
                    &nbsp;&nbsp;
                    <label><a href="#" onclick="jQuery('.statistics_filter_container :input[type!=button]').val(''); return false;"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_FILTER_RESET'); ?></a></label>
                </div>
            </div>
        </div>

        <div class="clr"></div>

        <a href="#" onclick="statisticsShow(); return false;" class="btn"><i class="icon-search"></i> <?php echo JText::_('PLG_LIKEBTN_STATISTICS_FILTER_SHOW'); ?></a>
        <br/><br/>

        <div id="statistics_wrapper" style="display: none;">
            <span><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TOTAL'); ?>:</span> <strong id="statistics_total">0</strong>
            <table id="statistics_container" class="adminlist table table-striped well">
                <thead>
                    <tr>
                        <th class="title"><input type="checkbox" value="all" onclick="statisticsItemsCheckbox(this)"></th>
                        <th class="title"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TABLE_ID'); ?></th>
                        <?php /*<th class="title"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TABLE_THUMBNAIL'); ?></th>*/ ?>
                        <th width="100%" class="title"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TABLE_TITLE'); ?></th>
                        <th class="title"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TABLE_LIKES'); ?></th>
                        <th class="title"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TABLE_DISLIKES'); ?></th>
                        <th class="title" style="min-width: 200px"><?php echo JText::_('PLG_LIKEBTN_STATISTICS_TABLE_LIKES_MINUS_DISLIKES'); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div class="alert alert-error" id="statistics_error" style="display: none;">
            <?php echo JText::_('PLG_LIKEBTN_STATISTICS_ERROR'); ?>
        </div>

        <div id="statistics_loader" style="display: none;">
            <img src="<?php echo JURI::root(); ?>plugins/content/likebtn/assets/images/ajax_loader.gif" />
        </div>

        <?php
    }

    /**
     * Get input html
     *
     * @return string
     */
    public function getInput() {
        return '';
    }
}