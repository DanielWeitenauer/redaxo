<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

class rex_api_article_name_sort extends rex_api_function
{
    public function execute()
    {
        $category_id = rex_request('category_id', 'int');
        $clang_id = rex_request('clang_id', 'int');
        $category = rex_category::get($category_id);
        $article_order = $category->getValue('article_order');

        if ($article_order && $article_order != 'priority, name') {
            switch ($article_order) {
                case 'name ASC':
                    $article_order = 'name DESC';
                    break;

                case 'name DESC':
                default:
                    $article_order = 'name ASC';
            }

            $sql = rex_sql::factory();
            $sql->setQuery('
                UPDATE 
                    '.rex::getTable('article').' 
                SET 
                    cat_article_order = "'.$article_order.'" 
                WHERE 
                    id = '.$category_id.' AND clang_id = '.$clang_id
            );

            rex_article_cache::delete($category_id);
        }

        return new rex_api_result(true, rex_i18n::msg('sort_order_changed'));
    }
}
