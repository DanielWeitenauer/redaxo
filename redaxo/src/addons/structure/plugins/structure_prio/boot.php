<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function (rex_extension_point $ep) {
    $article_actions = $ep->getSubject();
    $action_params = $ep->getParam('action_params');

    $article_actions['prio'] = ['article_priority_dec' => new rex_structure_article_priority_dec($action_params)] + $article_actions['prio'];
    $article_actions['prio']['article_priority_inc'] = new rex_structure_article_priority_inc($action_params);

    return $article_actions;
});
