<?php
/**
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');


class FacebookReviewsViewFacebookReviews extends JViewLegacy {
    function display($tpl = null) {
        $embedUrl = "https://apps.elfsight.com/embed/facebook-reviews/?utm_source=portals&utm_medium=joomla&utm_campaign=facebook-reviews&utm_content=sign-up";
        $this->url = $embedUrl;

        $params = json_encode([
            'user' => [
                'configEmail' => JFactory::getUser()->email
            ]
        ]);

        if (!empty($params)) {
            $this->url .= (parse_url($embedUrl, PHP_URL_QUERY) ? '&' : '?') . 'params=' . rawurlencode($params);
        }

        $document = JFactory::getDocument();

        $assetsPath = '/joomla/administrator/components/' . JFactory::getApplication()->input->get('option') . '/assets/';
        $document->addStyleSheet($assetsPath . 'elfsight-portal-admin.css');

        parent::display($tpl);
    }
}