<?php

/**
 * @copyright
 * @package        Easy Keyword Sitemap - EKS for Joomla! 3.x
 * @author         Viktor Vogel <admin@kubik-rubik.de>
 * @version        3.4.0-FREE - 2020-06-14
 * @link           https://kubik-rubik.de/eks-easy-keyword-sitemap
 *
 * @license        GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') || die('Restricted access');

use Joomla\CMS\{Plugin\CMSPlugin, Factory, Language\Text, MVC\Model\BaseDatabaseModel, Component\ComponentHelper, Access\Access, Filesystem\File, Router\Route, Helper\TagsHelper};
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

class PlgContentEasyKeywordSitemap extends CMSPlugin
{
    /**
     * @var array $eksParameters
     * @since 3.4.0-FREE
     */
    protected $eksParameters;

    /**
     * @var bool $autoloadLanguage
     * @since 3.4.0-FREE
     */
    protected $autoloadLanguage = true;

    /**
     * PlgContentEasyKeywordSitemap constructor.
     *
     * @param object $subject
     * @param array  $config
     *
     * @throws Exception
     * @since 3.4.0-FREE
     */
    public function __construct(object &$subject, array $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * Plugin is executed by the trigger onContentPrepare
     *
     * @param string   $context
     * @param object   $article
     * @param Registry $params
     * @param int|null $limitstart
     *
     * @throws Exception
     * @since 3.4.0-FREE
     */
    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        if (Factory::getApplication()->isClient('administrator')) {
            return;
        }

        if (strpos($context, 'com_content') === false || strpos($article->text, '{eks}') === false) {
            return;
        }

        if (preg_match_all('@{eks}(.*){/eks}@isU', $article->text, $matches, PREG_PATTERN_ORDER) > 0) {
            foreach ($matches[1] as $key => $match) {
                if (!empty($match)) {
                    $this->eksParameters = [];
                    $eksParametersTemp = explode('|', $match);

                    foreach ($eksParametersTemp as $eksParameterTemp) {
                        if (preg_match('@=@', $eksParameterTemp)) {
                            $eksParameterTemp = explode('=', $eksParameterTemp);

                            if (preg_match('@,@', $eksParameterTemp[1])) {
                                $eksParameterTemp[1] = array_map([$this, 'mb_trim'], explode(',', $eksParameterTemp[1]));

                                if ($eksParameterTemp[0] === 'keyword' || $eksParameterTemp[0] === 'nokeyword') {
                                    $eksParameterTemp[1] = array_map('strtolower', $eksParameterTemp[1]);
                                }
                            }

                            $this->eksParameters[$eksParameterTemp[0]] = $eksParameterTemp[1];

                            continue;
                        }

                        $this->eksParameters[$eksParameterTemp] = true;
                    }
                }

                $html = '';

                if (!empty($this->eksParameters['cache'])) {
                    $html = $this->loadCacheFile($match);
                }

                if (empty($html)) {
                    $htmlReplace = '<h2>Easy Keyword Sitemap</h2><p>' . Text::_('PLG_EASYKEYWORDSITEMAP_NOARTICLLESFOUND') . '</p>';
                    $articles = $this->articlesData();

                    if (!empty($articles)) {
                        $outputData = $this->keywordsData($articles);

                        if (!empty($outputData)) {
                            $htmlReplace = '';

                            if (!empty($this->eksParameters['alpha'])) {
                                $alphaIndex = $this->createAlphaIndex($outputData, $key);
                                $htmlReplace .= $alphaIndex[0];
                            }

                            foreach ($outputData as $keyword => $outputValues) {
                                if (!empty($alphaIndex[1])) {
                                    $keywordFirstChar = $this->firstCharAlphaIndex($keyword);

                                    if (in_array($keywordFirstChar, $alphaIndex[1])) {
                                        $htmlReplace .= '<a id="eks_' . StringHelper::strtolower($keywordFirstChar) . '_' . $key . '"></a>';
                                        $alphaIndexKey = array_search($keywordFirstChar, $alphaIndex[1]);
                                        unset($alphaIndex[1][$alphaIndexKey]);
                                    }
                                }

                                $htmlReplace .= '<h2>' . $keyword . '</h2>';
                                $htmlReplace .= '<ul>';

                                foreach ($outputValues as $outputValue) {
                                    $htmlReplace .= '<li><a href="' . $outputValue->link . '" title="' . $outputValue->title . '">' . $outputValue->title . '</a>';

                                    if (!empty($this->eksParameters['teaser']) && !empty($outputValue->metadesc)) {
                                        $htmlReplace .= '<br /><span class="eks_teaser">' . $outputValue->metadesc . '</span>';
                                    }

                                    $htmlReplace .= '</li>';
                                }

                                $htmlReplace .= '</ul>';
                            }
                        }
                    }

                    $html = '<div class="eks">' . $htmlReplace . '</div>';

                    if (!empty($this->eksParameters['cache'])) {
                        $this->writeCacheFile($match, $html);
                    }
                }

                $article->text = preg_replace('@(<p>)?{eks}' . preg_quote($match) . '{/eks}(</p>)?@is', $html, $article->text);
            }

            $css = '.eks {margin: 20px 0;}.eks_alphaindex {text-align: center;}.eks_teaser {font-size: 90%; font-style: italic;}';

            Factory::getDocument()->addStyleDeclaration($css);
        }
    }

    /**
     * Loads cache file if already available
     *
     * @param string $id
     *
     * @return string
     * @since 3.4.0-FREE
     */
    private function loadCacheFile(string $id): string
    {
        if (File::exists(JPATH_ROOT . '/cache/eks/' . md5($id))) {
            return file_get_contents(JPATH_ROOT . '/cache/eks/' . md5($id));
        }

        return '';
    }

    /**
     * Gets all articles depending of the different factors and restrictions, e.g. the user level
     *
     * @return mixed
     * @throws Exception
     * @since 3.4.0-FREE
     */
    private function articlesData()
    {
        BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

        /** @var ContentModelArticle $model */
        $model = BaseDatabaseModel::getInstance('Articles', 'ContentModel', ['ignore_request' => true]);
        $app = Factory::getApplication();
        $appParams = $app->getParams();
        $access = !ComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));

        $model->setState('list.start', 0);
        $model->setState('filter.published', 1);
        $model->setState('filter.access', $access);

        if (!empty($this->eksParameters['catid'])) {
            $model->setState('filter.category_id', $this->eksParameters['catid']);
        }

        $model->setState('filter.language', $app->getLanguageFilter());
        $model->setState('params', $appParams);
        $model->setState('list.ordering', 'a.title');

        if (!empty($this->eksParameters['ordering'])) {
            $orderingArray = ['id', 'title', 'catid', 'created', 'created_by', 'modified', 'ordering', 'hits', 'featured'];

            if (in_array($this->eksParameters['ordering'], $orderingArray)) {
                $model->setState('list.ordering', 'a.' . $this->eksParameters['ordering']);
            }
        }

        $model->setState('list.direction', 'ASC');

        if (!empty($this->eksParameters['direction'])) {
            $directionArray = ['ASC', 'DESC'];

            if (in_array($this->eksParameters['direction'], $directionArray)) {
                $model->setState('list.direction', $this->eksParameters['direction']);
            }
        }

        $articles = $model->getItems();
        $linkLogin = Route::_('index.php?option=com_users&view=login');

        foreach ($articles as &$article) {
            $article->slug = $article->id . ':' . $article->alias;
            $article->catslug = $article->catid . ':' . $article->category_alias;
            $article->link = $linkLogin;

            if ($access || in_array($article->access, $authorised)) {
                $article->link = Route::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug));
            }
        }

        return $articles;
    }

    /**
     * Extracts the keywords or tags from the articles. The tags are used if the parameter "tags" is entered in the syntax call.
     *
     * @param array $articles - Array of possible articles
     *
     * @return array $keyword_list - List of allowed keywords
     * @since 3.4.0-FREE
     */
    private function keywordsData(array $articles): array
    {
        $keywordsList = [];

        foreach ($articles as $article) {
            $this->createKeywordsList($keywordsList, $article);
        }

        $this->filterKeywordsList($keywordsList);
        ksort($keywordsList);

        return $keywordsList;
    }

    /**
     * Creates keywords list from the loaded articles
     *
     * @param array  $keywordsList
     * @param object $article
     */
    private function createKeywordsList(array &$keywordsList, object $article)
    {
        if (empty($this->eksParameters['tags'])) {
            if (!empty($article->metakey)) {
                $metakeyArray = array_map('trim', explode(',', $article->metakey));

                foreach ($metakeyArray as $metakey) {
                    $keywordsList[StringHelper::ucfirst($metakey)][] = $article;
                }
            }

            return;
        }

        $tagsHelper = new TagsHelper();
        $tags = $tagsHelper->getItemTags('com_content.article', $article->id);

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $keywordsList[StringHelper::ucfirst($tag->title)][] = $article;
            }
        }
    }

    /**
     * Filters keywords list if filter parameters are set
     *
     * @param array $keywordsList
     *
     * @since 3.4.0-FREE
     */
    private function filterKeywordsList(array &$keywordsList)
    {
        if (!empty($this->eksParameters['keyword'])) {
            $this->filterKeywordsListKeyword($keywordsList);
        } elseif (!empty($this->eksParameters['nokeyword'])) {
            $this->filterKeywordsListNoKeyword($keywordsList);
        }
    }

    /**
     * Filters keywords list if keyword filter is set
     *
     * @param array $keywordsList
     *
     * @since 3.4.0-FREE
     */
    private function filterKeywordsListKeyword(array &$keywordsList)
    {
        foreach ($keywordsList as $key => $value) {
            if (is_array($this->eksParameters['keyword'])) {
                if (!in_array(StringHelper::strtolower($key), $this->eksParameters['keyword'])) {
                    unset($keywordsList[$key]);
                }

                continue;
            }

            if (StringHelper::strtolower($key) != StringHelper::strtolower($this->eksParameters['keyword'])) {
                unset($keywordsList[$key]);
            }
        }
    }

    /**
     * Filters keywords list if nokeyword filter is set
     *
     * @param array $keywordsList
     *
     * @since 3.4.0-FREE
     */
    private function filterKeywordsListNoKeyword(array &$keywordsList)
    {
        foreach ($keywordsList as $key => $value) {
            if (is_array($this->eksParameters['nokeyword'])) {
                if (in_array(StringHelper::strtolower($key), $this->eksParameters['nokeyword'])) {
                    unset($keywordsList[$key]);
                }

                continue;
            }

            if (StringHelper::strtolower($key) == StringHelper::strtolower($this->eksParameters['nokeyword'])) {
                unset($keywordsList[$key]);
            }
        }
    }

    /**
     * Creates an alpha index with all items which are loaded in the sitemap
     *
     * @param array $dataArray - Keywords list array with all articles which are included in the output
     * @param int   $matchKey  - Number for the IDs which have to be unique
     *
     * @return array - HTML output of the alpha index and all first letters of allowed keywords for the creation of the anchors
     * @since 3.4.0-FREE
     */
    private function createAlphaIndex(array $dataArray, int $matchKey): array
    {
        $dataKeysArray = array_unique(array_map([$this, 'firstCharAlphaIndex'], array_keys($dataArray)));
        $dataRange = range('A', 'Z');

        if ($this->eksParameters['alpha'] === 'cyrillic') {
            $dataRange = [];

            foreach (range(chr(0xC0), chr(0xDF)) as $char) {
                $dataRange[] = iconv('CP1251', 'UTF-8', $char);
            }
        }

        $html = '<div class="eks_alphaindex">';

        foreach ($dataRange as $value) {
            $htmlValue = $value . ' ';

            if (in_array($value, $dataKeysArray)) {
                $htmlValue = '<a href="#eks_' . StringHelper::strtolower($value) . '_' . $matchKey . '">' . $value . '</a> ';
            }

            $html .= $htmlValue;
        }

        $html .= '</div>';

        return [$html, $dataKeysArray];
    }

    /**
     * Small helper function to get the first char of the transmitted string which is needed to create the alpha index
     *
     * @param string $value
     *
     * @return string - First character of the passed string
     * @since 3.4.0-FREE
     */
    private function firstCharAlphaIndex(string $value): string
    {
        return StringHelper::substr($value, 0, 1);
    }

    /**
     * Write the cache file with the complete output
     *
     * @param string $id
     * @param string $html
     *
     * @since 3.4.0-FREE
     */
    private function writeCacheFile(string $id, string $html)
    {
        File::write(JPATH_ROOT . '/cache/eks/' . md5($id), $html);
    }

    /**
     * Small helper function to trim UTF-8 encoded strings
     *
     * @param string $utf8String - UTF-8 encoded string
     *
     * @return string - Trimmed UTF-8 encoded string
     * @since 3.4.0-FREE
     */
    private function mb_trim(string $utf8String): string
    {
        return StringHelper::trim($utf8String);
    }
}
