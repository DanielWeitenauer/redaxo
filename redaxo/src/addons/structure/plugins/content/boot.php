<?php

/**
 * Page Content Addon.
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo5
 */

if (rex::isBackend() && rex::getUser()) {
    if (rex_be_controller::getCurrentPage() == 'content/edit') {
        rex_view::addCssFile($this->getAssetsUrl('fix_be_styles.css'));
    }
}

rex_perm::register('moveSlice[]', null, rex_perm::OPTIONS);
rex_complex_perm::register('modules', 'rex_module_perm');

if (rex::isBackend()) {
    rex_extension::register('STRUCTURE_CONTENT_SIDEBAR', function (rex_extension_point $ep) {
        $params = $ep->getParams();
        $subject = $ep->getSubject();

        $article = rex_article::get($params['article_id']);
        $category = $article instanceof rex_article ? $article->getCategory() : null;

        $action_vars = [
            'category' => $category,
            'edit_id' => $params['article_id'],
            'sql' => null,
            'pager' => null,
            'clang' => $params['clang'],
            'context' => new rex_context([
                'page' => rex_be_controller::getCurrentPage(),
                'article_id' => $params['article_id'],
                'clang' => $params['clang'],
                'ctype' => $params['ctype'],
            ]),
            'url_params' => [],
        ];

        // Predefine columns
        $actions = rex_structure_action_row::factory($action_vars);
        $actions->setColumns([
            'meta' => rex_structure_action_column::factory(),
            'status' => rex_structure_action_column::factory(),
            'actions' => rex_structure_action_column::factory(),
        ]);

        // Add fields
        $actions->getColumn('meta')
            ->setField('created_on', rex_structure_article_create_date::factory($action_vars))
            ->setField('created_by', rex_structure_article_create_user::factory($action_vars))
            ->setField('updated_on', rex_structure_article_update_date::factory($action_vars))
            ->setField('updated_by', rex_structure_article_update_user::factory($action_vars))
            ->setField('status', rex_structure_article_status::factory($action_vars)->setVar('class',''));
        $actions->getColumn('actions')
            ->setField('status_edit', rex_structure_article_edit::factory($action_vars))
            ->setField('status_delete', rex_structure_article_delete::factory($action_vars))
            ->setField('article2category', rex_structure_article2category::factory($action_vars))
            ->setField('article2startarticle', rex_structure_article2Startarticle::factory($action_vars))
            ->setField('article_move', rex_structure_article_move::factory($action_vars))
            ->setField('article_copy', rex_structure_article_copy::factory($action_vars))
            ->setField('content_copy', rex_structure_content_copy::factory($action_vars));

        // EXTENSION POINT to manipulate the $article_actions
        $actions = rex_extension::registerPoint(new rex_extension_point('PAGE_CONTENT_ARTICLE_ACTIONS', $actions, [
            'action_vars' => $action_vars,
        ]));

        $sidebar = $actions->getFragment('content_sidebar_actions.php');

        $fragment = new rex_fragment();
        $content = $fragment
            ->setVar('title', '<i class="rex-icon rex-icon-info"></i> '.rex_i18n::msg('metadata'), false)
            ->setVar('body', $sidebar, false)
            ->setVar('collapse', true)
            ->setVar('collapsed', false)
            ->parse('core/page/section.php');

        return $content.$subject;
    });

    rex_extension::register('PAGE_CHECKED', function () {
        if (rex_be_controller::getCurrentPagePart(1) == 'content') {
            rex_be_controller::getPageObject('structure')->setIsActive(true);
        }
    });

    if (rex_be_controller::getCurrentPagePart(1) == 'system') {
        rex_system_setting::register(new rex_system_setting_default_template_id());
    }

    rex_extension::register('CLANG_DELETED', function (rex_extension_point $ep) {
        $del = rex_sql::factory();
        $del->setQuery('delete from ' . rex::getTablePrefix() . "article_slice where clang_id='" . $ep->getParam('clang')->getId() . "'");
    });
} else {
    rex_extension::register('FE_OUTPUT', function (rex_extension_point $ep) {
        $clangId = rex_get('clang', 'int');
        if ($clangId && !rex_clang::exists($clangId)) {
            rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getStartId());
        }

        $content = $ep->getSubject();

        $article = new rex_article_content();
        $article->setCLang(rex_clang::getCurrentId());

        if ($article->setArticleId(rex_article::getCurrentId())) {
            $content .= $article->getArticleTemplate();
        } else {
            $content .= 'Kein Startartikel selektiert / No starting Article selected. Please click here to enter <a href="' . rex_url::backendController() . '">redaxo</a>';
            rex_response::sendPage($content);
            exit;
        }

        $art_id = $article->getArticleId();
        if ($art_id == rex_article::getNotfoundArticleId() && $art_id != rex_article::getSiteStartArticleId()) {
            rex_response::setStatus(rex_response::HTTP_NOT_FOUND);
        }

        // ----- inhalt ausgeben
        rex_response::sendPage($content, $article->getValue('updatedate'));
    });
}
