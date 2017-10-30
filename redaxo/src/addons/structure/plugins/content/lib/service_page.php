<?php

class rex_page_service
{
    /**
     * Make article status switchable
     * @see redaxo/src/addons/structure/pages/index.php
     * @param int $article_id
     * @param int $clang_id
     * @return string
     */
    protected static function getArticleStatus($article_id, $clang_id)
    {
        $article = rex_article::get($article_id, $clang_id);
        $artstart = rex_request('artstart', 'int');
        $catstart = rex_request('catstart', 'int');

        $user = rex::getUser();

        $article_status_types = rex_article_service::statusTypes();
        $article_status = $article_status_types[$article->getValue('status')][0];
        $article_class = $article_status_types[$article->getValue('status')][1];
        $article_icon = $article_status_types[$article->getValue('status')][2];

        if (!$user->getComplexPerm('structure')->hasCategoryPerm($article_id) || !$user->hasPerm('publishArticle[]')) {
            return '<span class="'.$article_class.' text-muted"><i class="rex-icon '.$article_icon.'"></i> '.$article_status.'</span>';
        }

        $context = new rex_context([
            'page' => 'content/edit',
            'category_id' => $article->getCategoryId(),
            'article_id' => $article_id,
            'clang' => $clang_id,
        ]);

        if ($article->isStartArticle()) {
            $article_link = $context->getUrl([
                'rex-api-call' => 'category_status',
                'catstart' => $catstart,
                'category-id' => $article->getCategoryId(),
            ]);
        } else {
            $article_link = $context->getUrl([
                'rex-api-call' => 'article_status',
                'artstart' => $artstart
            ]);
        }

        return '<a class="'.$article_class.'" href="'.$article_link.'"><i class="rex-icon '.$article_icon.'"></i> '.$article_status.'</a>';
    }
}
