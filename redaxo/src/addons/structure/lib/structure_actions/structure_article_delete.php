<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_delete extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $category_id = rex_article::get($article_id)->getCategoryId();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('delete'),
            'icon' => 'rex-icon rex-icon-delete',
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'title' => rex_i18n::msg('delete'),
                'data-confirm' => rex_i18n::msg('delete').'?',
            ],
        ];

        if (!rex_article::get($article_id)->isStartArticle() && rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'page' => 'structure',
                'rex-api-call' => 'article_delete',
                'article_id' => $article_id,
                'category_id' => $category_id,
            ]);
            $button_params['url'] = $context->getUrl($url_params, false);
            $button_params['attributes']['class'][] = 'btn-default';
        } else {
            $button_params['attributes']['class'][] = 'text-muted';
        }

        return $this->getButtonFragment($button_params);
    }
}
