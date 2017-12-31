<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function (rex_extension_point $ep) {
    /** @var rex_structure_action_row $article_rows */
    $article_rows = $ep->getSubject();
    $action_params = $ep->getParam('action_vars');

    $article_rows->setColumn('priority', new rex_structure_action_column());
    $article_rows->getColumn('priority')
        ->setField('article_priority_dec', new rex_structure_article_priority_dec($action_params))
        ->setField('article_priority', new rex_structure_article_priority_ext($action_params))
        ->setField('article_priority_inc', new rex_structure_article_priority_inc($action_params));

    return $article_rows;
});
