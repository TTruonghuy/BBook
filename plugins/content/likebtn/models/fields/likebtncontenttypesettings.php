<?php

/**
 * @project  LikeBtn Like Button
 * @author   LikeBtn.com (info@likebtn.com)
 * @copyright (Copyright (C) 2013 by LikeBtn. All rights reserved.
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

/**
 * Provides LikeBtn settings for content types
 *
 * @package     Joomla.Plugin
 * @subpackage  Likebtn
 * @since       2.5.5
 */
class JFormFieldLikebtncontenttypesettings extends JFormField {

    // The field class must know its own type through the variable $type.
    protected $type = 'Likebtncontenttypesettings';

    public function getLabel() {

        // Run sunchronization
        require_once(dirname(__FILE__).'/../../likebtn.php');
        require_once(dirname(__FILE__).'/../../likebtn.class.php');
        $likebtn = new LikeBtn(plgContentLikebtn::getPluginParams());
        $likebtn->runSyncVotes();

        JLoader::register("plgContentLikebtn", JPATH_PLUGINS.DIRECTORY_SEPARATOR."content".DIRECTORY_SEPARATOR."likebtn".DIRECTORY_SEPARATOR."likebtn.php");

        $doc = &JFactory::getDocument();
        $doc->addScript(JURI::root() . "plugins/content/likebtn/assets/js/admin.js");
        $doc->addScript(JURI::root() . "plugins/content/likebtn/assets/js/select2.js");
        $doc->addScript(JURI::root() . "plugins/content/likebtn/assets/js/bootstrap-colorpicker.js");
        $doc->addStyleSheet(JURI::root() . "plugins/content/likebtn/assets/css/admin.css");
        $doc->addStyleSheet(JURI::root() . "plugins/content/likebtn/assets/css/select2.css");
        $doc->addStyleSheet(JURI::root() . "plugins/content/likebtn/assets/css/bootstrap-colorpicker.css");

        $language = &JFactory::getLanguage();
        $likebtn_website_locale = substr($language->getTag(), 0, 2);

        if (!in_array($likebtn_website_locale, plgContentLikebtn::$likebtn_website_locales)) {
            $likebtn_website_locale = 'en';
        }
        $doc->addScript('//likebtn.com/' . $likebtn_website_locale . '/js/donate_generator.js');

        // Get a db connection.
        $db = JFactory::getDbo();

        // K2
        jimport('joomla.application.component.controller');
        // Check if component is installed
        $db->setQuery("SELECT enabled FROM #__extensions WHERE name = 'com_k2'");
        $k2_enabled = $db->loadResult();
        if ($k2_enabled) {
            // Get K2 categories
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select(array('id', 'name'));
            $query->from('#__k2_categories');
            $query->where('published = 1');
            $query->order('ordering ASC');
            $db->setQuery($query);
            $k2_categories = $db->loadObjectList();

            $k2_categories_options = array();
            foreach ($k2_categories as $k2_category) {
                $selected = false;
                $k2_categories_options[] = JHtml::_(
                    'select.option',
                    (string) $k2_category->id,
                    $k2_category->name,
                    'value',
                    'text',
                    $selected
                );
            }
        }

        $content_types = plgContentLikebtn::getSupportedContentTypes($db, $k2_enabled);

        ?>
        <div class="tabbable tabs-left">
            <ul id="likebtnContentTypeButtonTabs" class="nav nav-tabs">
            <?php foreach ($content_types as $content_type_index => $content_type): ?>
                <?php
                $content_type_show = '0';
                if (empty($this->value)) {
                    $this->value = [];
                }
                if (!empty($this->value[$content_type->type_alias])) {
                    $content_type_show = $this->value[$content_type->type_alias]['show'];
                }
                ?>
                <li class="<?php if ($content_type_index == 0): ?>active<?php endif ?>"><a data-toggle="tab" href="#content_type_pane_<?php echo $content_type->type_id ?>"><?php echo $content_type->type_title ?> <span <?php if ($content_type_show == '1'): ?>class="icon-save"<?php endif ?>></span></a></li>
            <?php endforeach ?>
            </ul>
            <div id="likebtnContentTypeButtonTabContent" class="tab-content">
                <?php foreach ($content_types as $content_type_index => $content_type): ?>
                    <?php
                        $name_prefix = 'jform[params][' . plgContentLikebtn::ADMIN_FIELD_NAME . '][' . $content_type->type_alias . ']';

                        if (empty($this->value[$content_type->type_alias])) {
                            $this->value[$content_type->type_alias] = array();
                        }
                        if (empty($this->value[$content_type->type_alias]['settings'])) {
                            $this->value[$content_type->type_alias]['settings'] = array();
                        }
                        $value = plgContentLikebtn::prepareGeneralSettings($this->value[$content_type->type_alias]);
                        $value['settings'] = plgContentLikebtn::prepareSettings($this->value[$content_type->type_alias]['settings']);

                        // Build a list of content types.
                        $content_types_options = array();

                        $selected = false;
                        if (empty($this->value[$content_type->type_alias]['use_settings_from']) || (string) $this->value[$content_type->type_alias]['use_settings_from'] == '') {
                            $selected = true;
                        }
                        $content_types_options[] = JHtml::_(
                            'select.option',
                            '',
                            '',
                            'value',
                            'text',
                            $selected
                        );

                        foreach ($content_types as $sub_content_type) {
                            if ($sub_content_type->type_alias == $content_type->type_alias) {
                                continue;
                            }
                            $selected = false;
                            if (!empty($this->value[$content_type->type_alias]['use_settings_from']) &&
                                (string)$this->value[$content_type->type_alias]['use_settings_from'] == $sub_content_type->type_alias)
                            {
                                $selected = true;
                            }
                            $content_types_options[] = JHtml::_(
                                'select.option',
                                (string) $sub_content_type->type_alias,
                                $sub_content_type->type_title,
                                'value',
                                'text',
                                $selected
                            );
                        }

                        // Style list.
                        $style_options = plgContentLikebtn::getStyles();
                        $style_options = array('custom' => JText::_('PLG_LIKEBTN_CUSTOM_THEME')) + $style_options;

                        $voting_effects = plgContentLikebtn::$voting_effects;
                        array_unshift($voting_effects, array("" => ""));

                        $counter_frmts = plgContentLikebtn::$counter_frmts;
                        foreach ($counter_frmts as $frmt => $frmt_name) {
                            $counter_frmts[$frmt] = JText::_($frmt_name);
                        }

                        // Language list.
                        $language_options = array();
                        //$language_options['auto'] = 'auto - ' . JText::_('Detect from client browser');
                        $languages = plgContentLikebtn::getLanguages();
                        foreach ($languages as $language_code=>$language_info) {
                            $language_options[$language_code] = $language_info;
                        }

                        // Exclude categories
                        if ($content_type->type_alias == 'com_k2.item' && !empty($k2_categories_options)) {
                            // K2 categories
                            $exclude_categories_html = JHtml::_('select.genericlist', $k2_categories_options, $name_prefix . '[exclude_categories][]', array('multiple'=>true, 'class'=>'likebtn_select_category'), 'value', 'text', $value['exclude_categories']);
                        } else {
                            // Joomla categories
                            $exclude_categories_html = $this->getJHTMLSelectCategory('exclude_categories', $content_type->type_alias, $value['exclude_categories'], array('class'=>'likebtn_select_category'));
                        }
                    ?>

                    <div class="tab-pane <?php if ($content_type_index == 0): ?>active<?php endif ?>" id="content_type_pane_<?php echo $content_type->type_id ?>" >
                        <legend><?php echo $content_type->type_title; ?></legend>
                        <div class="control-group">
                            <div class="control-label">
                                <label title=""><strong><?php echo JText::_('PLG_LIKEBTN_BUTTONS_CONTENT_TYPE_NAME'); ?></strong></label>
                            </div>
                            <div class="controls">
                                <span class="disabled readonly"><?php echo $content_type->type_alias; ?></span>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label title=""><strong><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW'); ?></strong></label>
                            </div>
                            <div class="controls">
                                <?php echo $this->getJHTMLSelectRadio('show', $content_type->type_alias,  array(), $value['show'], array('onclick' => "contentTypeShowChange(this, '{$content_type->type_alias}', '{$content_type->type_id}')")); ?>
                            </div>
                        </div>
                        <div id="content_type_container_<?php echo $content_type->type_alias; ?>" <?php if ($value['show'] != '1'): ?>style="display:none"<?php endif ?> >
                            <?php if ($value['show']): ?>
                            <div class="control-group">
                                <div class="control-label">
                                    <label title=""><strong><?php echo JText::_('PLG_LIKEBTN_BUTTONS_USE'); ?></strong></label>
                                </div>
                                <div class="controls">
                                    <?php echo JHtml::_('select.genericlist', $content_types_options, $name_prefix . '[use_settings_from]', array('onchange' => "useSettingsFromChange(this, '{$content_type->type_alias}')"), 'value', 'text', $value['use_settings_from']); ?><br/>
                                    <span class="disabled readonly">
                                        <?php echo JText::_('PLG_LIKEBTN_BUTTONS_CHOOSE'); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <?php echo JText::_('PLG_LIKEBTN_BUTTONS_YOU_CAN_FIND_DESCRIPTION'); ?>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <label title=""><strong><?php echo JText::_('PLG_LIKEBTN_BUTTONS_RICH_SNIPPETS'); ?></strong></label>
                                </div>
                                <div class="controls">
                                    <?php echo $this->getJHTMLSelectRadio('settings][rich_snippet', $content_type->type_alias,  array(), $value['settings']['rich_snippet']); ?> 
                                    <a href="<?php echo JText::_('PLG_LIKEBTN_BUTTONS_RICH_SNIPPETS_LINK'); ?>" target="_blank"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_WHAT_IS_RICH_SNIPPET'); ?></a>
                                </div>
                            </div>
                            <div id="use_settings_from_container_<?php echo $content_type->type_alias; ?>" <?php if ($value['use_settings_from']): ?>style="display:none"<?php endif ?> class="use_settings_from_container">


                                <ul id="likebtnContentTypeSettings<?php echo $content_type->type_id ?>Tabs" class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_display_conditions"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_DISPLAY'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_style_and_language"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_STYLE'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_appearance_and_behaviour"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_APPEARANCE'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_voting"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_VOTING'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_counter"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_COUNTER'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_popup"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_statistics"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHARING'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_loader"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LOADER'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_tooltips"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_TOOLTIPS'); ?></a></li>
                                        <?php /*<li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_domains"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_DOMAINS'); ?></a></li>*/ ?>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_debugging"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_DEBUGGING'); ?></a></li>
                                        <li><a data-toggle="tab" href="#content_type_settings_pane_<?php echo $content_type->type_id ?>_labels"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LABELS'); ?></a></li>
                                </ul>
                                <div id="likebtnContentTypeSettings<?php echo $content_type->type_id ?>TabContent" class="tab-content">
                                    <div class="tab-pane active" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_display_conditions" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_CONTENT'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php /*echo JHtml::_('select.genericlist', $content_view_mode_options, $name_prefix . '[content_view_mode]', array(), 'value', 'text', $value['content_view_mode']);*/ ?>
                                                   <?php /*echo JHtml::_('select.radiolist', $content_view_mode_options, $name_prefix . '[content_view_mode]', null, 'value', 'text', $value['content_view_mode']);*/ ?>
                                                   <?php echo $this->getJHTMLSelectRadio('content_view_mode', $content_type->type_alias,  array('full'=>JText::_('PLG_LIKEBTN_BUTTONS_FULL'), 'excerpt'=>JText::_('PLG_LIKEBTN_BUTTONS_EXCERPT'), 'both'=>JText::_('PLG_LIKEBTN_BUTTONS_BOTH')), $value['content_view_mode']); ?>
                                                   <br/>
                                                   <span class="disabled readonly">
                                                       <?php echo JText::_('PLG_LIKEBTN_BUTTONS_CHOOSE_CONTENT'); ?>
                                                   </span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_EXCLUDE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $exclude_categories_html; ?>
                                                    <br/>
                                                    <span class="disabled readonly">
                                                        <?php echo JText::_('PLG_LIKEBTN_BUTTONS_SELECT'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_USER_AUTHORIZATION'); ?></label>
                                                </div>
                                                <div class="controls">
                                                   <?php echo $this->getJHTMLSelectRadio('user_logged_in', $content_type->type_alias,  array('logged_in'=>JText::_('PLG_LIKEBTN_BUTTONS_LOGGED_IN'), 'not_logged_in'=>JText::_('PLG_LIKEBTN_BUTTONS_NOT_LOGGED_IN'), 'all'=>JText::_('PLG_LIKEBTN_BUTTONS_FOR_ALL')), $value['user_logged_in']); ?>
                                                   <br/>
                                                   <span class="disabled readonly">
                                                       <?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW_THE'); ?>
                                                   </span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POSITION'); ?></label>
                                                </div>
                                                <div class="controls">
                                                   <?php echo $this->getJHTMLSelectRadio('position', $content_type->type_alias,  array('top'=>JText::_('PLG_LIKEBTN_BUTTONS_TOP'), 'bottom'=>JText::_('PLG_LIKEBTN_BUTTONS_BOTTOM'), 'both'=>JText::_('PLG_LIKEBTN_BUTTONS_TOP_AND')), $value['position']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_ALIGNMENT'); ?></label>
                                                </div>
                                                <div class="controls">
                                                   <?php echo $this->getJHTMLSelectRadio('alignment', $content_type->type_alias,  array('left'=>JText::_('PLG_LIKEBTN_BUTTONS_LEFT'), 'center'=>JText::_('PLG_LIKEBTN_BUTTONS_CENTER'), 'right'=>JText::_('PLG_LIKEBTN_BUTTONS_RIGHT')), $value['alignment']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_INSERT'); ?></label>
                                                </div>
                                                <div class="controls">
                                                   <?php echo $this->getJHTMLText('html_before', $content_type->type_alias, $value['html_before']); ?>
                                                   <span class="disabled readonly">
                                                       <?php echo JText::_('PLG_LIKEBTN_BUTTONS_HTML_CODE_BEFORE'); ?>
                                                   </span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_INSERT_AFTER'); ?></label>
                                                </div>
                                                <div class="controls">
                                                   <?php echo $this->getJHTMLText('html_after', $content_type->type_alias, $value['html_after'],  array('cols'=>30, 'rows'=>5)); ?>
                                                   <span class="disabled readonly">
                                                       <?php echo JText::_('PLG_LIKEBTN_BUTTONS_HTML_CODE_AFTER'); ?>
                                                   </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_style_and_language" >
                                        <div class="well well-small">
                                            <?php
                                                $theme_type = 'predefined';
                                                if ($value['settings']['style'] == 'custom') {
                                                    $theme_type = 'custom';
                                                }
                                            ?>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_THEME'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo JHtml::_('select.genericlist', $style_options, $name_prefix . '[settings][style]', array('onchange' => "themeChange(this, '{$content_type->type_alias}', '{$content_type->type_id}')"), 'value', 'text', $value['settings']['style']); ?>
                                                </div>
                                            </div>
                                            <div id="custom_theme_container_<?php echo $content_type->type_alias; ?>" <?php if ($theme_type != 'custom'): ?>style="display:none"<?php endif ?>>
                                                
                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_BUTTON_SIZE'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                       <?php echo $this->getJHTMLNumber('settings][btn_size', $content_type->type_alias, $value['settings']['btn_size'], array("min"=>5, "max"=>500, "maxlength"=>3)); ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_FONT_SIZE'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                       <?php echo $this->getJHTMLNumber('settings][f_size', $content_type->type_alias, $value['settings']['f_size'], array("min"=>5, "max"=>500, "maxlength"=>3)); ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_SIZE'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                       <?php echo $this->getJHTMLNumber('settings][icon_size', $content_type->type_alias, $value['settings']['icon_size'], array("min"=>5, "max"=>500, "maxlength"=>3)); ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_L'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                        <select class="likebtn_icon_list" name="jform[params][<?php echo plgContentLikebtn::ADMIN_FIELD_NAME ?>][<?php echo $content_type->type_alias ?>][settings][icon_l]" >
                                                            <option value=""></option>
                                                            <?php foreach (plgContentLikebtn::$likebtn_icons as $icon): ?>
                                                                <option value="<?php echo $icon; ?>" <?php if ($value['settings']['icon_l'] == $icon): ?>selected="selected"<?php endif ?> ><?php echo $icon; ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_D'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                        <select class="likebtn_icon_list" name="jform[params][<?php echo plgContentLikebtn::ADMIN_FIELD_NAME ?>][<?php echo $content_type->type_alias ?>][settings][icon_d]" >
                                                            <option value=""></option>
                                                            <?php foreach (plgContentLikebtn::$likebtn_icons as $icon): ?>
                                                                <option value="<?php echo $icon; ?>" <?php if ($value['settings']['icon_d'] == $icon): ?>selected="selected"<?php endif ?> ><?php echo $icon; ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_L_URL'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                       <?php echo $this->getJHTMLText('settings][icon_l_url', $content_type->type_alias, $value['settings']['icon_l_url']); ?>
                                                       <?php if ($value['settings']['icon_l_url']): ?>
                                                            <div><img src="<?php echo htmlspecialchars($value['settings']['icon_l_url']) ?>" class="likebtn_icon_prv"/></div>
                                                       <?php endif ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_L_URL_V'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                       <?php echo $this->getJHTMLText('settings][icon_l_url_v', $content_type->type_alias, $value['settings']['icon_l_url_v']); ?>
                                                       <?php if ($value['settings']['icon_l_url_v']): ?>
                                                            <div><img src="<?php echo htmlspecialchars($value['settings']['icon_l_url_v']) ?>" class="likebtn_icon_prv"/></div>
                                                       <?php endif ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_D_URL'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                       <?php echo $this->getJHTMLText('settings][icon_d_url', $content_type->type_alias, $value['settings']['icon_d_url']); ?>
                                                       <?php if ($value['settings']['icon_d_url']): ?>
                                                            <div><img src="<?php echo htmlspecialchars($value['settings']['icon_d_url']) ?>" class="likebtn_icon_prv"/></div>
                                                       <?php endif ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_D_URL_V'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                       <?php echo $this->getJHTMLText('settings][icon_d_url_v', $content_type->type_alias, $value['settings']['icon_d_url_v']); ?>
                                                       <?php if ($value['settings']['icon_d_url_v']): ?>
                                                            <div><img src="<?php echo htmlspecialchars($value['settings']['icon_d_url_v']) ?>" class="likebtn_icon_prv"/></div>
                                                       <?php endif ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_L_C'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][icon_l_c', $content_type->type_alias, $value['settings']['icon_l_c'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_L_C_V'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][icon_l_c_v', $content_type->type_alias, $value['settings']['icon_l_c_v'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_D_C'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][icon_d_c', $content_type->type_alias, $value['settings']['icon_d_c'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_ICON_D_C_V'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][icon_d_c_v', $content_type->type_alias, $value['settings']['icon_d_c_v'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_LABEL_C'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][label_c', $content_type->type_alias, $value['settings']['label_c'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_LABEL_C_V'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][label_c_v', $content_type->type_alias, $value['settings']['label_c_v'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_COUNTER_L_C'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][counter_l_c', $content_type->type_alias, $value['settings']['counter_l_c'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_COUNTER_D_C'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][counter_d_c', $content_type->type_alias, $value['settings']['counter_d_c'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_BG_C'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][bg_c', $content_type->type_alias, $value['settings']['bg_c'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_BG_C_V'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][bg_c_v', $content_type->type_alias, $value['settings']['bg_c_v'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_BRDR_C'); ?></label>
                                                    </div>
                                                    <div class="controls cp-group">
                                                        <?php echo $this->getJHTMLText('settings][brdr_c', $content_type->type_alias, $value['settings']['brdr_c'], array("class"=>"likebtn_cp")); ?>
                                                        <span class="input-group-addon"><i></i></span>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_F_FAMILY'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                        <?php echo JHtml::_('select.genericlist', plgContentLikebtn::$likebtn_fonts, $name_prefix . '[settings][f_family]', array(), 'value', 'text', $value['settings']['f_family']); ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_LABEL_FS'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                        <?php echo JHtml::_('select.genericlist', plgContentLikebtn::$likebtn_font_styles, $name_prefix . '[settings][label_fs]', array(), 'value', 'text', $value['settings']['label_fs']); ?>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label title=""><?php echo JText::_('PLG_LIKEBTN_CUSTOM_COUNTER_FS'); ?></label>
                                                    </div>
                                                    <div class="controls">
                                                        <?php echo JHtml::_('select.genericlist', plgContentLikebtn::$likebtn_font_styles, $name_prefix . '[settings][counter_fs]', array(), 'value', 'text', $value['settings']['counter_fs']); ?>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LANGUAGE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo JHtml::_('select.genericlist', $language_options, $name_prefix . '[settings][lang]', array(), 'value', 'text', $value['settings']['lang']); ?> 
                                                    <span class="disabled readonly"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SEND_TRANSLATION'); ?></span>
                                                </div>
                                            </div>
                                            <div class="alert alert-info">
                                                <?php echo JText::_('PLG_LIKEBTN_BUTTONS_YOU_CAN_FIND'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_appearance_and_behaviour" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_VERT'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][vert', $content_type->type_alias,  array(), $value['settings']['vert']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW_LIKE_LABEL'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][show_like_label', $content_type->type_alias,  array(), $value['settings']['show_like_label']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW_DISLIKE_LABEL'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][show_dislike_label', $content_type->type_alias,  array(), $value['settings']['show_dislike_label']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][like_enabled', $content_type->type_alias,  array(), $value['settings']['like_enabled']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW_DISLIKE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][dislike_enabled', $content_type->type_alias,  array(), $value['settings']['dislike_enabled']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_ICON_LIKE_SHOW'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][icon_like_show', $content_type->type_alias,  array(), $value['settings']['icon_like_show']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_ICON_DISLIKE_SHOW'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][icon_dislike_show', $content_type->type_alias,  array(), $value['settings']['icon_dislike_show']); ?>
                                                </div>
                                            </div>

                                            <div class="alert alert-info">
                                                <?php echo JText::_('PLG_LIKEBTN_BUTTONS_YOU_CAN_FIND'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_voting" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_EF_VOTING'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo JHtml::_('select.genericlist', $voting_effects, $name_prefix . '[settings][ef_voting]', array(), 'value', 'text', $value['settings']['ef_voting']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_VOTING_DISABLED'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][display_only', $content_type->type_alias,  array(), $value['settings']['display_only']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_VOTING_CANCELLABLE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][voting_cancelable', $content_type->type_alias,  array(), $value['settings']['voting_cancelable']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_VOTING_BOTH'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][voting_both', $content_type->type_alias,  array(), $value['settings']['voting_both']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_ALLOW_UNLIKE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][unlike_allowed', $content_type->type_alias,  array(), $value['settings']['unlike_allowed']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_UNLIKE_ALLOWED'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][like_dislike_at_the_same_time', $content_type->type_alias,  array(), $value['settings']['like_dislike_at_the_same_time']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_REVOTE_PERIOD'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][revote_period', $content_type->type_alias, $value['settings']['revote_period']); ?>
                                                    <br/><br/><span class="disabled readonly"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_REVOTE_PERIOD_HINT'); ?></span>
                                                </div>
                                            </div>

                                            <div class="alert alert-info">
                                                <?php echo JText::_('PLG_LIKEBTN_BUTTONS_YOU_CAN_FIND'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_counter" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_COUNTER_TYPE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][counter_type', $content_type->type_alias,  array('number'=>JText::_('PLG_LIKEBTN_BUTTONS_NUMBER'), 'percent'=>JText::_('PLG_LIKEBTN_BUTTONS_PERCENT'), 'subtract_dislikes'=>JText::_('PLG_LIKEBTN_BUTTONS_SUBSTRACT_DISLIKES'), 'single_number'=>JText::_('PLG_LIKEBTN_BUTTONS_SINGLE_NUMBER')), $value['settings']['counter_type']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_VOTES_COUNTER'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][counter_clickable', $content_type->type_alias,  array(), $value['settings']['counter_clickable']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_COUNTER_SHOW'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][counter_show', $content_type->type_alias,  array(), $value['settings']['counter_show']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_COUNTER_FRMT'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo JHtml::_('select.genericlist', $counter_frmts, $name_prefix . '[settings][counter_frmt]', array(), 'value', 'text', $value['settings']['counter_frmt']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_COUNTER_PADDING'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][counter_padding', $content_type->type_alias, $value['settings']['counter_padding']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_COUNTER_ZERO_SHOW'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][counter_zero_show', $content_type->type_alias,  array(), $value['settings']['counter_zero_show']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_COUNTER_COUNT'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][counter_count', $content_type->type_alias,  array(), $value['settings']['counter_count']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_popup" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW_POPUP'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][popup_enabled', $content_type->type_alias,  array(), $value['settings']['popup_enabled']); ?>
                                                    <span class="disabled readonly"><?php echo JText::_('PLG_LIKEBTN_POPUP_DISABLE_TEXT'); ?></span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_OFFER_TO_SHARE_AFTER_DISLIKING'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][popup_dislike', $content_type->type_alias,  array(), $value['settings']['popup_dislike']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_ON_LOAD'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][popup_on_load', $content_type->type_alias,  array(), $value['settings']['popup_on_load']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_POSITION'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][popup_position', $content_type->type_alias, array('top'=>JText::_('PLG_LIKEBTN_BUTTONS_POPUP_TOP'), 'right'=>JText::_('PLG_LIKEBTN_BUTTONS_POPUP_RIGHT'), 'bottom'=>JText::_('PLG_LIKEBTN_BUTTONS_POPUP_BOTTOM'), 'left'=>JText::_('PLG_LIKEBTN_BUTTONS_POPUP_LEFT')), $value['settings']['popup_position']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_STYLE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][popup_style', $content_type->type_alias,  array('light'=>JText::_('PLG_LIKEBTN_BUTTONS_LIGHT'), 'dark'=>JText::_('PLG_LIKEBTN_BUTTONS_DARK')), $value['settings']['popup_style']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_WIDTH'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][popup_width', $content_type->type_alias, $value['settings']['popup_width']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_HIDE_ON_OUTSIDE_CLICK'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][popup_hide_on_outside_click', $content_type->type_alias,  array(), $value['settings']['popup_hide_on_outside_click']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW_COPYRIGHT'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][show_copyright', $content_type->type_alias,  array(), $value['settings']['show_copyright']); ?>
                                                    <span class="disabled readonly"><?php echo JText::_('PLG_LIKEBTN_WHITE_LABEL_TEXT'); ?></span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_HTML'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][popup_html', $content_type->type_alias, $value['settings']['popup_html']); ?>
                                                    <span class="disabled readonly">(PRO, VIP, ULTRA)</span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_DONATE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][popup_donate', $content_type->type_alias, $value['settings']['popup_donate']); ?> <a href="javascript:likebtnDG('jform_params__content_type_settings__com_content_article__settings__popup_donate_', true);void(0);" title="<?php echo JText::_('PLG_LIKEBTN_BUTTONS_CONFIGURE_DONATE'); ?>"><img class="popup_donate_trigger" src="<?php echo JURI::root(); ?>plugins/content/likebtn/assets/images/popup_donate.png" alt="<?php echo JText::_('PLG_LIKEBTN_BUTTONS_CONFIGURE_DONATE'); ?>"></a>
                                                    <span class="disabled readonly">(VIP, ULTRA)</span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_CONTENT_ORDER'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][popup_content_order', $content_type->type_alias, $value['settings']['popup_content_order']); ?>
                                                </div>
                                            </div>


                                            <div class="alert alert-info">
                                                <?php echo JText::_('PLG_LIKEBTN_BUTTONS_YOU_CAN_FIND'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_statistics" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_OFFER_TO_SHARE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][share_enabled', $content_type->type_alias,  array(), $value['settings']['share_enabled']); ?>
                                                    <span class="disabled readonly">(PLUS, PRO, VIP, ULTRA)<br/><br/><?php echo JText::_('PLG_LIKEBTN_BUTTONS_OFFER_TO_SHARE_HINT'); ?></span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_POSITION_SHARE_SIZE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][share_size', $content_type->type_alias, array('small'=>JText::_('PLG_LIKEBTN_BUTTONS_SHARE_SIZE_SMALL'), 'medium'=>JText::_('PLG_LIKEBTN_BUTTONS_SHARE_SIZE_MEDIUM'), 'large'=>JText::_('PLG_LIKEBTN_BUTTONS_SHARE_SIZE_LARGE')), $value['settings']['share_size']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_ADDTHIS'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][addthis_pubid', $content_type->type_alias, $value['settings']['addthis_pubid']); ?>
                                                    <span class="disabled readonly">(PRO, VIP, ULTRA)<br/><br/><?php echo JText::_('PLG_LIKEBTN_BUTTONS_ALLOWS_TO_COLLECT'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_ADDTHIS_CODES'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][addthis_service_codes', $content_type->type_alias, $value['settings']['addthis_service_codes']); ?>
                                                    <span class="disabled readonly">(PRO, VIP, ULTRA)<br/><br/><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SERVICE_CODES'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_loader" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LAZY_LOAD'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][lazy_load', $content_type->type_alias,  array(), $value['settings']['lazy_load']); ?>
                                                    <span class="disabled readonly"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LAZY_LOAD_HINT'); ?></span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LOADER_SHOW'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][loader_show', $content_type->type_alias,  array(), $value['settings']['loader_show']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LOADER_IMG'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][loader_image', $content_type->type_alias, $value['settings']['loader_image']); ?>
                                                    <span class="disabled readonly"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LOADER_IMG_DESCRIPTION'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_tooltips" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_SHOW_TOOLTIPS'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][tooltip_enabled', $content_type->type_alias,  array(), $value['settings']['tooltip_enabled']); ?>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_TOOLTIP_LIKE_SHOW_ALWAYS'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][tooltip_like_show_always', $content_type->type_alias,  array(), $value['settings']['tooltip_like_show_always']); ?>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_TOOLTIP_DISLIKE_SHOW_ALWAYS'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][tooltip_dislike_show_always', $content_type->type_alias,  array(), $value['settings']['tooltip_dislike_show_always']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_domains" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title="">[domain_from_parent]</label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][domain_from_parent', $content_type->type_alias,  array(), $value['settings']['domain_from_parent']); ?>
                                                    <span class="disabled readonly"><?php echo JText::_('PLG_LIKEBTN_BUTTONS_DOMAIN_FROM_PARENT'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_debugging" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_CALLBACK'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][event_handler', $content_type->type_alias, $value['settings']['event_handler']); ?>
                                                    <span class="disabled readonly"><br/><br/>
                                                        <?php echo JText::_('PLG_LIKEBTN_BUTTONS_THE_PROVIDED_FUNCTION'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_INFO_MESSAGE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][info_message', $content_type->type_alias,  array(), $value['settings']['info_message']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="content_type_settings_pane_<?php echo $content_type->type_id ?>_labels" >
                                        <div class="well well-small">
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_RTL'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLSelectRadio('settings][rtl', $content_type->type_alias,  array(), $value['settings']['rtl']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_LIKE_BUTTON_LABEL'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_like', $content_type->type_alias, $value['settings']['i18n_like']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_DISLIKE_BUTTON_LABEL'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_dislike', $content_type->type_alias, $value['settings']['i18n_dislike']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_AFTER_LIKE_BUTTON_LABEL'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_after_like', $content_type->type_alias, $value['settings']['i18n_after_like']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_AFTER_DISLIKE_BUTTON_LABEL'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_after_dislike', $content_type->type_alias, $value['settings']['i18n_after_dislike']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_TOOLTIP'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_like_tooltip', $content_type->type_alias, $value['settings']['i18n_like_tooltip']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_DISLIKE_TOOLTIP'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_dislike_tooltip', $content_type->type_alias, $value['settings']['i18n_dislike_tooltip']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_TOOLTIP_AFTER_LIKING'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_unlike_tooltip', $content_type->type_alias, $value['settings']['i18n_unlike_tooltip']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_TOOLTIP_AFTER_DISLIKING'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_undislike_tooltip', $content_type->type_alias, $value['settings']['i18n_undislike_tooltip']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_TEXT_SHARE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_share_text', $content_type->type_alias, $value['settings']['i18n_share_text']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_CLOSE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_popup_close', $content_type->type_alias, $value['settings']['i18n_popup_close']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_POPUP_SHARING_DISABLED'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_popup_text', $content_type->type_alias, $value['settings']['i18n_popup_text']); ?>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_I18N_POPUP_DONATE'); ?></label>
                                                </div>
                                                <div class="controls">
                                                    <?php echo $this->getJHTMLText('settings][i18n_popup_donate', $content_type->type_alias, $value['settings']['i18n_popup_donate']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clr"></div>
                            <div class="control-group">
                                <div class="control-label">
                                    <label title=""><?php echo JText::_('PLG_LIKEBTN_BUTTONS_DEMO'); ?></label>
                                </div>
                                <div class="controls">
                                    <?php echo plgContentLikebtn::getMarkup($content_type->type_alias, 'demo', array(), true, true); ?>
                                </div>
                            </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <?php echo JText::_('PLG_LIKEBTN_BUTTONS_SAVE'); ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class="control-group" style="visibility:hidden">
                            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
        <script type="text/javascript">
            loadJQuery();
            settingsScript();
        </script>
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

    /**
     * Get radio HTML
     *
     */
    public function getJHTMLSelectRadio($name, $type_alias, $options = array(), $selected=null, $attribs=null) {
        // Joomla 2.5
        JLoader::register("JFormFieldRadio", JPATH_LIBRARIES.DIRECTORY_SEPARATOR."joomla".DIRECTORY_SEPARATOR."form".DIRECTORY_SEPARATOR."fields".DIRECTORY_SEPARATOR."radio.php");

        $form_field = new JFormFieldRadio();
        if (!$options) {
            $options = array(
                '1' => JText::_('JYES'),
                '0' => JText::_('JNO'),
            );
        }
        $attribs['class'] = 'btn-group';
        $attribs['default'] = '0';
        return $this->getJHTMLInput($form_field, $name, $type_alias, 'radio', $selected, $attribs, $options);
    }

    /**
     * Get select HTML
     */
    public function getJHTMLSelectCategory($name, $type_alias, $selected=array(), $attribs=null, $options = array()) {
        // Joomla 2.5
        JLoader::register("JFormFieldCategory", JPATH_LIBRARIES.DIRECTORY_SEPARATOR."joomla".DIRECTORY_SEPARATOR."form".DIRECTORY_SEPARATOR."fields".DIRECTORY_SEPARATOR."category.php");

        $form_field = new JFormFieldCategory();
        $attribs['extension'] = 'com_content';
        $attribs['default'] = '0';
        $attribs['multiple'] = 'multiple';
        return $this->getJHTMLInput($form_field, $name, $type_alias, 'category', $selected, $attribs, $options);
    }

    /**
     * Get text input HTML
     *
     */
    public function getJHTMLText($name, $type_alias, $selected=array(), $attribs=null) {
        // Joomla 2.5
        JLoader::register("JFormFieldText", JPATH_LIBRARIES.DIRECTORY_SEPARATOR."joomla".DIRECTORY_SEPARATOR."form".DIRECTORY_SEPARATOR."fields".DIRECTORY_SEPARATOR."text.php");

        $form_field = new JFormFieldText();
        return $this->getJHTMLInput($form_field, $name, $type_alias, 'text', $selected, $attribs);
    }

    /**
     * Get number input HTML
     *
     */
    public function getJHTMLNumber($name, $type_alias, $value = '', $attribs=null) {
        $html = '<input name="jform[params]['.plgContentLikebtn::ADMIN_FIELD_NAME.'][' . $type_alias . '][' . $name . ']" value="'.(int)$value.'" aria-invalid="false" type="number" ';

        if (is_array($attribs)) {
            foreach ($attribs as $attr_name => $attr_value) {
                $html .= " {$attr_name}=\"$attr_value\" ";
            }
        }

        $html .= ' />';

        return $html;
    }

    /**
     * Get HTML of the field
     *
     */
    public function getJHTMLInput($form_field, $name, $type_alias, $type, $selected=array(), $attribs=array(), $options=array()) {

        $xml = '<field name="jform[params][' . plgContentLikebtn::ADMIN_FIELD_NAME . '][' . $type_alias . '][' . $name . ']" type="' . $type . '" ';

        if (is_array($attribs)) {
            foreach ($attribs as $attr_name => $attr_value) {
                $xml .= " {$attr_name}=\"$attr_value\" ";
            }
        }
        if (empty($attribs['onclick'])) {
            $attribs['onclick'] = '';
        }
        if (count($options)) {
            $xml .= '>';
            foreach ($options as $key=>$value) {
                $xml .= '<option value="' . $key . '" onclick="' . $attribs['onclick'] . '">' . $value . '</option>';
            }
            $xml .= '</field>';
        } else {
            $xml .= '/>';
        }

        $form_field->setup(simplexml_load_string($xml), $selected);

        return $form_field->getInput();
    }

}
