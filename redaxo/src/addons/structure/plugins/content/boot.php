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

        $action_params = [
            'edit_id' => $params['article_id'],
            #'sql' => '',
            #'pager' => $artPager,
            'clang' => $params['clang'],
            'context' => new rex_context([
                'page' => rex_be_controller::getCurrentPage(),
                'article_id' => $params['article_id'],
                'clang' => $params['clang'],
                'ctype' => $params['ctype'],
            ]),
            'url_params' => [],
        ];
        $article_actions = [
            'meta' => [
                'created_on' => new rex_structure_article_create_date($action_params),
                'created_by' => new rex_structure_article_create_user($action_params),
                'updated_on' => new rex_structure_article_update_date($action_params),
                'updated_by' => new rex_structure_article_update_user($action_params),
            ],
            'status' => [
                'article_delete' => new rex_structure_article_delete($action_params),
                'article_status' => new rex_structure_article_status($action_params),
            ],
            'action' => [
                'article2category' => new rex_structure_article2category($action_params),
                'article2startarticle' => new rex_structure_article2Startarticle($action_params),
                'article_move' => new rex_structure_article_move($action_params),
                'article_copy' => new rex_structure_article_copy($action_params),
            ],
            'content_action' => [
                'content_copy' => new rex_structure_content_copy($action_params),
            ],
        ];

        // EXTENSION POINT to manipulate the $article_actions array
        $article_actions = rex_extension::registerPoint(new rex_extension_point('PAGE_CONTENT_ARTICLE_ACTIONS', $article_actions, $action_params));

        $fragment = new rex_fragment([
            'article' => rex_article::get($params['article_id'], $params['clang']),
            'article_status_types' => rex_article_service::statusTypes(),
            'article_actions' => $article_actions,
        ]);
        $sidebar =  $fragment->parse('sidebar_actions.php');

        $fragment = new rex_fragment();
        $fragment->setVar('title', '<i class="rex-icon rex-icon-info"></i> '.rex_i18n::msg('metadata'), false);
        $fragment->setVar('body', $sidebar, false);
        $fragment->setVar('collapse', true);
        $fragment->setVar('collapsed', false);
        $content = $fragment->parse('core/page/section.php');

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
