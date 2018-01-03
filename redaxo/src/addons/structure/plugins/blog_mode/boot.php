<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

if (rex::isBackend() && rex::getUser()) {
    if (rex_be_controller::getCurrentPagePart(1) == 'structure') {
        rex_view::addCssFile($this->getAssetsUrl('fix_be_styles.css'));
    }

    rex_extension::register('PAGE_STRUCTURE_ARTICLE_ORDER', function(rex_extension_point $ep) {
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

    rex_extension::register('PAGE_STRUCTURE_CATEGORY_ACTIONS', function(rex_extension_point $ep) {
        /** @var rex_structure_action_row $category_row */
        $category_row = $ep->getSubject();
        $action_vars = $ep->getParam('action_vars');

        $category_row->setColumn('type', rex_structure_action_column::factory($action_vars));
        $category_row->getColumn('type')
             ->setField('category_type', rex_blog_mode_info::factory($action_vars));

        return $category_row;
    }, rex_extension::LATE);

    rex_extension::register('PAGE_STRUCTURE_ARTICLE_ACTIONS', function(rex_extension_point $ep) {
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
                    ->unsetColumn('priority')
                    ->unsetColumn('template')
                    ->unsetColumn('date');

                // Remove unnecessary fields
                if ($article_row->hasColumn('status')) {
                    $article_row->getColumn('status')
                                ->unsetField('article_edit');
                }
                if ($article_row->hasColumn('action')) {
                    $article_row->getColumn('action')
                                ->unsetField('article2startarticle')
                                ->unsetField('article2category');
                }

                // Add createdate and updatedate and make them sortable
                $article_row
                    ->setColumn('create_date', rex_structure_action_column::factory($action_vars))
                    ->getColumn('create_date')
                    ->setHead(rex_blog_mode_article_header_createdate_sortable::factory($action_vars))
                    ->setField('article_create_date', rex_structure_article_create_date::factory($action_vars));
                $article_row
                    ->setColumn('update_date', rex_structure_action_column::factory($action_vars))
                    ->getColumn('update_date')
                    ->setHead(rex_blog_mode_article_header_updatedate_sortable::factory($action_vars))
                    ->setField('article_update_date', rex_structure_article_update_date::factory($action_vars));

                // Make name sortable
                if ($article_row->hasColumn('article')) {
                    $article_row->getColumn('article')
                                ->setHead(rex_blog_mode_article_header_name_sortable::factory($action_vars));
                }
            }
        }

        return $article_row;
    }, rex_extension::LATE);
}
