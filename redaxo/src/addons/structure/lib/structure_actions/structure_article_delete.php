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

        if (rex_article::get($article_id)->isStartArticle() || !rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            return '';
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'page' => 'structure',
            'rex-api-call' => 'article_delete',
            'article_id' => $article_id,
            'category_id' => $category_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('delete'),
            'icon' => 'rex-icon rex-icon-delete',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
                'title' => rex_i18n::msg('delete'),
                'data-confirm' => rex_i18n::msg('delete').'?',
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
