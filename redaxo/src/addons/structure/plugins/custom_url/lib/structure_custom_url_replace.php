<?php
/**
 * @package structure/custom_url
 */
class structure_custom_url_replace extends structure_custom_url
{
    /**
     * Set extension point
     */
    public static function init()
    {
        rex_extension::register('URL_REWRITE', [__CLASS__, '_replaceUrl'], rex_extension::EARLY);
    }

    public static function _replaceUrl(rex_extension_point $ep)
    {
        $params = $ep->getParams();

        $article_id = $params['id'];
        $clang_id = $params['clang'];

        $data = self::getCustomUrlData($article_id, $clang_id);
        $url_type = isset($data['url_type']) ? $data['url_type'] : self::URL_TYPE_DEFAULT;

        switch ($url_type) {
            case self::URL_TYPE_LANG_SWITCH:
                if (isset($data['clang_id'])) {
                    $return = rex_getUrl($article_id, $data['clang_id'], $params['params'], $params['separator']);
                }
                break;

            case self::URL_TYPE_ARTICLE:
                if (isset($data['article_id'])) {
                    $return = rex_getUrl($data['article_id'], $clang_id, $params['params'], $params['separator']);
                }
                break;

            case self::URL_TYPE_CLANG:
                if (isset($data['article_id']) && isset($data['clang_id'])) {
                    $return = rex_getUrl($data['article_id'], $data['clang_id'], $params['params'], $params['separator']);
                }
                break;

            case self::URL_TYPE_MEDIA:
                if (isset($data['file'])) {
                    $return = rex_url::media($data['file']);
                }
                break;

                /*case self::URL_TYPE_REMOVE_ROOT_CAT:
                  /*$curUrl = $SEO42_IDS[$articleId][$clangId]['url'];
                    $newUrl = seo42_utils::removeRootCatFromUrl($curUrl, $clangId);

                    if ($newUrl != '') {
                        // same as SEO42_URL_TYPE_USERDEF_INTERN
                        $SEO42_URLS[$newUrl] = $SEO42_URLS[$SEO42_IDS[$articleId][$clangId]['url']];
                        unset($SEO42_URLS[$SEO42_IDS[$articleId][$clangId]['url']]);

                        $SEO42_IDS[$articleId][$clangId] = array('url' => $newUrl);
                    }
                break;*/

                /*case self::URL_TYPE_INTERNAL:
                  /*$customUrl = $jsonData['custom_url'];

                    if ($SEO42_IDS[$articleId][$clangId]['url'] != $customUrl) { // only if custom url ist different then auto url
                        $SEO42_URLS[$customUrl] = $SEO42_URLS[$SEO42_IDS[$articleId][$clangId]['url']];
                        unset($SEO42_URLS[$SEO42_IDS[$articleId][$clangId]['url']]);
                    }

                    $SEO42_IDS[$articleId][$clangId] = array('url' => $customUrl);
                break;*/

            case self::URL_TYPE_EXTERNAL:
                if (isset($data['custom_url'])) {
                    $return = $data['custom_url'];
                }
                break;

                /*case self::URL_TYPE_FUNCTION:
                  /*if ($jsonData['no_url']) {
                        unset($SEO42_URLS[$SEO42_IDS[$articleId][$clangId]['url']]);
                    }
                break;*/

                /*case self::URL_TYPE_NONE:
                  /*unset($SEO42_URLS[$SEO42_IDS[$articleId][$clangId]['url']]);

                    $SEO42_IDS[$articleId][$clangId] = array('url' => '');
                break;*/

            case self::URL_TYPE_DEFAULT:
            default:
                $return = '';
        }

        dump([
            'type' => $url_type,
            'data' => $data,
            'custom_url' => $return,
        ]);

        return $return;
    }
}
