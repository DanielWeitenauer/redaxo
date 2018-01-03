<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category2Article extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $category_id = $this->getVar('edit_id');
        $article = rex_article::get($category_id);
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        if (!$article->isStartArticle() || !$user->hasPerm('article2category[]') || !$user->getComplexPerm('structure')->hasCategoryPerm($article->getCategoryId())) {
            return '';
        }

        // Check if category has children, if it does, its type cannot be changed
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT pid FROM '.rex::getTable('article').' WHERE parent_id=? LIMIT 1', [$category_id]);

        if ($sql->getRows() != 0) {
            return '';
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'rex-api-call' => 'category2article',
            'article_id' => $category_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('content_toarticle'),
            'icon' => 'rex-icon rex-icon-article',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
                'title' => rex_i18n::msg('content_toarticle'),
                'data-confirm' => rex_i18n::msg('content_toarticle').'?',
            ],
        ];

        return $this->getButtonFragment($button_params);
    }

    /**
     * @return string
     */
    public function getModal()
    {
        return '';
    }
}
