<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_delete extends rex_structure_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function getField()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $category_id = $article->getCategoryId();
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');



        $field_params = [
            $this->hasVar('hidden_label') && $this->getVar('hidden_label') ? 'hidden_label' : 'label' => rex_i18n::msg('delete'),
            'icon' => 'rex-icon rex-icon-delete',
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'title' => rex_i18n::msg('delete'),
                'data-confirm' => rex_i18n::msg('delete').'?',
            ],
        ];

        // Active state
        if (!rex_article::get($article_id)->isStartArticle() && $user->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'page' => 'structure',
                'rex-api-call' => 'article_delete',
                'article_id' => $article_id,
                'category_id' => $category_id,
                rex_api_article_delete::getUrlParams(),
            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
            $field_params['attributes']['class'][] = 'btn-default';
        }
        // Inactive state
        else {
            $field_params['attributes']['class'][] = 'text-muted disabled';
        }

        return $this->getFragment($field_params);
    }
}
