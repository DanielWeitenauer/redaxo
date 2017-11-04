<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ORDER', function (rex_extension_point $ep) {
    $article_order = $ep->getSubject();
    $category_id = $ep->getParam('category_id');

    $category = rex_category::get($category_id);
    if ($category instanceof rex_category) {
        $new_article_order = rex_session('blog_mode::article_order', 'string', $category->getValue('article_order'));
        if ($new_article_order) {
            $article_order = $new_article_order;
        }
    }

    return $article_order;
});

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function (rex_extension_point $ep) {
    $article_actions = $ep->getSubject();
    $action_params = $ep->getParam('action_params');
    $article = rex_article::get($action_params['edit_id']);

    if ($article instanceof rex_article) {
        $category = $article->getCategory();

        if ($category instanceof rex_category && $category->getValue('article_order')) {
            unset($article_actions['prio']);
            unset($article_actions['template']);
            unset($article_actions['status']['article_edit']);
            unset($article_actions['action']['article2startarticle']);
            unset($article_actions['action']['article2category']);

            $article_actions['create_date'] = $article_actions['date'];
            unset($article_actions['date']);
            $article_actions['update_date']['article_update_date'] = new rex_structure_article_update_date($action_params);
        }
    }

    return $article_actions;
}, rex_extension::LATE);

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS_HEADER', function (rex_extension_point $ep) {
    $article_actions_header = $ep->getSubject();
    $action_params_header = $ep->getParam('action_params_header');
    $category = rex_category::get($action_params_header['edit_id']);

    if ($category instanceof rex_category && $category->getValue('article_order')) {
        $article_actions_header['article_name']['article_name'] = new rex_structure_article_name_sortable($action_params_header);
        $article_actions_header['create_date']['article_create_date'] = new rex_structure_article_createdate_sortable($action_params_header);
        $article_actions_header['update_date']['article_update_date'] = new rex_structure_article_updatedate_sortable($action_params_header);
    }

    return $article_actions_header;
}, rex_extension::LATE);

