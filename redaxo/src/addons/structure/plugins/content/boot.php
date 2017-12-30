<?php

/**
 * Page Content Addon.
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo5
 */

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
        $actions = new rex_structure_action_row($action_vars);
        $actions['meta'] = new rex_structure_action_column();
        $actions['status'] = new rex_structure_action_column();
        $actions['action'] = new rex_structure_action_column();
        $actions['content_action'] = new rex_structure_action_column();

        // Add fields
        $actions['meta']
            ->setField('created_on', new rex_structure_article_create_date($action_vars))
            ->setField('created_by', new rex_structure_article_create_user($action_vars))
            ->setField('updated_on', new rex_structure_article_update_date($action_vars))
            ->setField('updated_by', new rex_structure_article_update_user($action_vars));

        $actions['status']
            ->setField('status_status', new rex_structure_article_status($action_vars));
        $actions['action']
            ->setField('status_edit', new rex_structure_article_edit($action_vars))
            ->setField('status_delete', new rex_structure_article_delete($action_vars))
            ->setField('article2category', new rex_structure_article2category($action_vars))
            ->setField('article2startarticle', new rex_structure_article2Startarticle($action_vars))
            ->setField('article_move', new rex_structure_article_move($action_vars))
            ->setField('article_copy', new rex_structure_article_copy($action_vars));
        $actions['content_action']
            ->setField('content_copy', new rex_structure_content_copy($action_vars));

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
