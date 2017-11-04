<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

class rex_api_article_createdate_sort extends rex_api_function
{
    public function execute()
    {
        $category_id = rex_request('category_id', 'int');
        $clang_id = rex_request('clang_id', 'int');
        $category = rex_category::get($category_id);
        $article_order = rex_session('blog_mode::article_order', 'string', $category->getValue('article_order'));

        if ($article_order && $article_order != 'priority, name') {
            switch ($article_order) {
                case 'createdate DESC':
                    $article_order = 'createdate ASC';
                    break;

                case 'createdate ASC':
                default:
                    $article_order = 'createdate DESC';
                    break;
            }

            rex_set_session('blog_mode::article_order', $article_order);
        }

        return new rex_api_result(true, rex_i18n::msg('sort_order_changed'));
    }
}
