<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ORDER', function (rex_extension_point $ep) {
    $article_order = $ep->getSubject();
    $category_id = $ep->getParam('category_id');
    $clang_id = $ep->getParam('clang_id');

    $category = rex_category::get($category_id);
    if ($category instanceof rex_category) {
        $article_orders = rex_session('blog_mode::article_order', 'array');
        $new_article_order = isset($article_orders[$category_id][$clang_id]) ? $article_orders[$category_id][$clang_id] : $category->getValue('article_order');

        if ($new_article_order) {
            $article_order = $new_article_order;
        }
    }

    return $article_order;
});

rex_extension::register('PAGE_STRUCTURE_CATEGORY_ACTIONS', function (rex_extension_point $ep) {
    /** @var rex_structure_action_row $category_row */
    $category_row = $ep->getSubject();
    $action_vars = $ep->getParam('action_vars');

    $category_row->setColumn('type', new rex_structure_action_column($action_vars));
    $category_row->getColumn('type')
        ->setField('blog_mode', new rex_blog_mode_info($action_vars));

    return $category_row;
}, rex_extension::LATE);

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function (rex_extension_point $ep) {
    /** @var rex_structure_action_row $article_row */
    $article_row = $ep->getSubject();
    $action_vars = $ep->getParam('action_vars');
    $article = rex_article::get($action_vars['edit_id']);

    if ($article instanceof rex_article) {
        $category = $article->getCategory();

        if ($category instanceof rex_category && $category->getValue('article_order') != 'priority, name') {
            // Remove unnecessary columns
            $article_row
                ->unsetColumn('prio')
                ->unsetColumn('template')
                ->unsetColumn('date');

            // Remove unnecessary fields
            $article_row->getColumn('status')
                ->unsetField('article_edit');
            $article_row->getColumn('action')
                ->unsetField('article2startarticle')
                ->unsetField('article2category');

            // Add createdate and updatedate and make them sortable
            $article_row
                ->setColumn('create_date', new rex_structure_action_column($action_vars))
                ->getColumn('create_date')
                ->setHead(new rex_blog_mode_article_header_createdate_sortable($action_vars))
                ->setField('article_create_date', new rex_structure_article_create_date($action_vars));
            $article_row
                ->setColumn('update_date', new rex_structure_action_column($action_vars))
                ->getColumn('update_date')
                ->setHead(new rex_blog_mode_article_header_updatedate_sortable($action_vars))
                ->setField('article_update_date', new rex_structure_article_update_date($action_vars));

            // Make name sortable
            $article_row->getColumn('article')
                ->setHead(new rex_blog_mode_article_header_name_sortable($action_vars));
        }
    }

    return $article_row;
}, rex_extension::LATE);
