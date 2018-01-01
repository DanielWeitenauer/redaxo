<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */
if (rex::isBackend() && rex::getUser()) {
    if (rex_be_controller::getCurrentPagePart(1) == 'structure') {
        rex_view::addCssFile($this->getAssetsUrl('fix_be_styles.css'));
    }

    rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function(rex_extension_point $ep) {
        /** @var rex_structure_action_row $article_rows */
        $article_rows  = $ep->getSubject();
        $action_params = $ep->getParam('action_vars');

        $article_rows->setColumn('priority', rex_structure_action_column::factory($action_params));
        $article_rows->getColumn('priority')
                     ->setField('article_priority_dec', rex_structure_article_priority_dec::factory($action_params))
                     ->setField('article_priority', rex_structure_article_priority_ext::factory($action_params))
                     ->setField('article_priority_inc', rex_structure_article_priority_inc::factory($action_params));

        return $article_rows;
    });
}
