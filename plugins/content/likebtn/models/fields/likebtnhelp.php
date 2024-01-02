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
class JFormFieldLikebtnhelp extends JFormField {

    // The field class must know its own type through the variable $type.
    protected $type = 'Likebtnhelp';

    public function getLabel() {

        ?>
        <?php echo JText::_('PLG_LIKEBTN_HELP_LINK'); ?> | 
        <?php echo JText::_('PLG_LIKEBTN_CONTACT'); ?>
        <?php

        /*?>
        <ul>
            <li><a href="#identifier"><?php echo JText::_('PLG_LIKEBTN_HELP_IDENTIFIER_TITLE'); ?></a></li>
            <li><a href="#shortcode"><?php echo JText::_('PLG_LIKEBTN_HELP_SHORTCODE_TITLE'); ?></a></li>
            <li><a href="#shortcode_off"><?php echo JText::_('PLG_LIKEBTN_HELP_SHORTCODE_OFF_TITLE'); ?></a></li>
        </ul>
        <br/>
        <h3 id="identifier"><?php echo JText::_('PLG_LIKEBTN_HELP_IDENTIFIER_TITLE'); ?></h3>
        <?php echo JText::_('PLG_LIKEBTN_HELP_IDENTIFIER_BODY'); ?>
        <br/><br/>

        <h3 id="identifier"><?php echo JText::_('PLG_LIKEBTN_HELP_SHORTCODE_TITLE'); ?></h3>
        <?php echo JText::_('PLG_LIKEBTN_HELP_SHORTCODE_BODY'); ?>
        <br/><br/>

        <h3 id="identifier"><?php echo JText::_('PLG_LIKEBTN_HELP_SHORTCODE_OFF_TITLE'); ?></h3>
        <?php echo JText::_('PLG_LIKEBTN_HELP_SHORTCODE_OFF_BODY'); ?>
        <br/><br/>

        <?php*/
    }

    /**
     * Get input html
     */
    public function getInput() {
        return '';
    }
}