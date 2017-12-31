<?php
/**
 * @package structure/custom_url
 */
class structure_custom_url
{
    /**
     * Type constant values are compatible to seo42
     */
    const URL_TYPE_DEFAULT = 0;

    const URL_TYPE_LANG_SWITCH = 4;
    const URL_TYPE_ARTICLE = 7;
    const URL_TYPE_CLANG = 1;
    const URL_TYPE_MEDIA = 3;

    const URL_TYPE_INTERNAL = 2;
    const URL_TYPE_EXTERNAL = 9;
    const URL_TYPE_FUNCTION = 8;

    const URL_TYPE_REMOVE_ROOT_CAT = 6;

    const URL_TYPE_NONE = 5;

    /**
     * @api
     * @return array
     */
    public static function getUrlOptions()
    {
        return [
            self::URL_TYPE_DEFAULT => self::msg('url_type_default'),

            self::URL_TYPE_LANG_SWITCH => self::msg('url_type_lang_switch'),
            self::URL_TYPE_ARTICLE => self::msg('url_type_article'),
            self::URL_TYPE_CLANG => self::msg('url_type_clang'),
            self::URL_TYPE_MEDIA => self::msg('url_type_media'),

            #self::URL_TYPE_INTERNAL => self::msg('url_type_internal'),
            self::URL_TYPE_EXTERNAL => self::msg('url_type_external'),
            #self::URL_TYPE_FUNCTION => self::msg('url_type_function'),

            self::URL_TYPE_REMOVE_ROOT_CAT => self::msg('url_type_remove_root_cat'),

            #self::URL_TYPE_NONE => self::msg('url_type_none'),
        ];
    }

    /**
     * @api
     * @param int $article_id
     * @param int $clang_id
     *
     * @return array
     */
    public static function getCustomUrlData($article_id, $clang_id = 0)
    {
        if ($clang_id < 1) {
            $clang_id = rex_clang::getCurrentId();
        }

        $article = rex_article::get($article_id, $clang_id);

        if (!$article instanceof rex_article) {
            return null;
        }

        $data = $article->getValue('structure_custom_url');

        return json_decode($data, true);
    }

    /**
     * @param $string
     *
     * @return string
     */
    protected static function msg($string)
    {
        return rex_i18n::msg('custom_url_'.$string);
    }
}
