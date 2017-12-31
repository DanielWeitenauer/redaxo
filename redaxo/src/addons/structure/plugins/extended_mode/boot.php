<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_extension::register('PAGE_STRUCTURE_CATEGORY_ACTIONS', function (rex_extension_point $ep) {
    /** @var rex_structure_action_row $category_row */
    $category_row = $ep->getSubject();
    $action_vars = $ep->getParam('action_vars');
    $category = rex_category::get($action_vars['edit_id']);

    if ($category instanceof rex_category) {
        $category_row->getColumn('action')
            ->setField('category2article', new rex_structure_category2article($action_vars))
            ->setField('category_move', new rex_structure_category_move($action_vars));
    }

    /** @var rex_structure_action_column $column */
    foreach ($category_row->getColumns() as $column) {
        /** @var rex_structure_action_field $field */
        foreach ($column->getFields() as $field) {
            $field->setVar('hide_label', true);
        }
    }

    return $category_row;
});

rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function (rex_extension_point $ep) {
    /** @var rex_structure_action_row $article_row */
    $article_row = $ep->getSubject();
    $action_vars = $ep->getParam('action_vars');
    $article = rex_article::get($action_vars['edit_id']);

    if ($article instanceof rex_article) {
        $article_row->getColumn('action')
            ->setField('article2category', new rex_structure_article2category($action_vars))
            ->setField('article2startarticle',  new rex_structure_article2startarticle($action_vars))
            ->setField('article_move',  new rex_structure_article_move($action_vars))
            ->setField('article_copy',  new rex_structure_article_copy($action_vars));
    }

    /** @var rex_structure_action_column $column */
    foreach ($article_row->getColumns() as $column) {
        /** @var rex_structure_action_field $field */
        foreach ($column->getFields() as $field) {
            $field->setVar('hide_label', true);
        }
    }

    return $article_row;
});
