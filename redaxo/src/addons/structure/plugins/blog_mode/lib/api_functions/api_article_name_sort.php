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
        $article_orders = rex_session('blog_mode::article_order', 'array');
        $article_order = isset($article_orders[$category_id][$clang_id]) ? $article_orders[$category_id][$clang_id] : $category->getValue('article_order');

        if ($article_order && $article_order != 'priority, name') {
            switch ($article_order) {
                case 'name ASC':
                    $article_order = 'name DESC';
                    break;

                case 'name DESC':
                default:
                    $article_order = 'name ASC';
            }

            $article_orders[$category_id][$clang_id] = $article_order;
            rex_set_session('blog_mode::article_order', $article_orders);
        }

        return new rex_api_result(true, rex_i18n::msg('sort_order_changed'));
    }
}
