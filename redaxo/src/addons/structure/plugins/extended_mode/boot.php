<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_extension::register('PAGE_STRUCTURE_CATEGORY_ACTIONS', function (rex_extension_point $ep) {
    $category_actions = $ep->getSubject();
    $action_params = $ep->getParam('action_params');
    $category = rex_category::get($action_params['edit_id']);

    if ($category instanceof rex_category) {
        $category_actions['action']['category2article'] = new rex_structure_category2article($action_params);
        $category_actions['action']['category_move'] = new rex_structure_category_move($action_params);
    }

    return $category_actions;
}, rex_extension::LATE);

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function (rex_extension_point $ep) {
    $article_actions = $ep->getSubject();
    $action_params = $ep->getParam('action_params');
    $article = rex_article::get($action_params['edit_id']);

    if ($article instanceof rex_article) {
        $article_actions['action']['article2category'] = new rex_structure_article2category($action_params);
        $article_actions['action']['article2startarticle'] =  new rex_structure_article2startarticle($action_params);
        $article_actions['action']['article_move'] =  new rex_structure_article_move($action_params);
        $article_actions['action']['article_copy'] =  new rex_structure_article_copy($action_params);
    }

    return $article_actions;
}, rex_extension::LATE);
