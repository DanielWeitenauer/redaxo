<?php
/**
 * @package redaxo\structure
 *
 * @internal
 */
class rex_api_article_priority extends rex_api_function
{
    public function execute()
    {
        $article_id = rex_request('article_id', 'int');
        $clang = rex_request('clang', 'int');
        $new_prio = rex_request('new_priority', 'int');

        $article = rex_article::get($article_id);
        $category_id = $article->getCategoryId();

        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            throw new rex_api_exception('user has no permission for this category!');
        }

        $result = new rex_api_result(true, rex_article_service::editArticle($article_id, $clang, [
            'priority' => $new_prio,
            'name' => $article->getName(),
            'template_id' => $article->getTemplateId(),
        ]));

        return $result;
    }
}
