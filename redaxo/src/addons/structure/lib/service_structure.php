<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

class rex_structure_service
{
    const ARTICLE_ORDER_VALUE = 'article_order';

    /**
     * @param int $category_id
     * @return string
     */
    public static function getArticleOrder($category_id)
    {
        $return = 'priority, name';

        $category = rex_category::get($category_id);

        if ($category instanceof rex_category) {
            $return = $category->getValue(self::ARTICLE_ORDER_VALUE);
        }

        return $return;
    }
}
