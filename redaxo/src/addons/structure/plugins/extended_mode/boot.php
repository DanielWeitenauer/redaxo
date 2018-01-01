<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

if (rex::isBackend() && rex::getUser()) {
    if (rex_be_controller::getCurrentPagePart(1) == 'structure') {
        rex_view::addCssFile($this->getAssetsUrl('fix_be_styles.css'));
    }

    rex_extension::register('PAGE_STRUCTURE_CATEGORY_ACTIONS', function(rex_extension_point $ep) {
        /** @var rex_structure_action_row $category_row */
        $category_row = $ep->getSubject();
        $action_vars  = $ep->getParam('action_vars');
        $category     = rex_category::get($action_vars['edit_id']);

        if (!$category_row->hasColumn('action')) {
            $category_row->setColumn('action', rex_structure_action_column::factory($action_vars));
        }
        if ($category instanceof rex_category) {
            $category_row->getColumn('action')
                         ->setField('category2article', rex_structure_category2article::factory($action_vars))
                         ->setField('category_move', rex_structure_category_move::factory($action_vars));
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

    rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function(rex_extension_point $ep) {
        /** @var rex_structure_action_row $article_row */
        $article_row = $ep->getSubject();
        $action_vars = $ep->getParam('action_vars');
        $article = rex_article::get($action_vars['edit_id']);


        if (!$article_row->hasColumn('action')) {
            $article_row->setColumn('action', rex_structure_action_column::factory($action_vars));
        }
        if ($article instanceof rex_article) {
            $article_row->getColumn('action')
                ->setField('article2category', rex_structure_article2category::factory($action_vars))
                ->setField('article2startarticle', rex_structure_article2startarticle::factory($action_vars))
                ->setField('article_move', rex_structure_article_move::factory($action_vars))
                ->setField('article_copy', rex_structure_article_copy::factory($action_vars));
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
}
