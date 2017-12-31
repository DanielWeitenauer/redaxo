<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article2category extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        /** @var rex_context $context */
        $context = $this->getVar('context');

        // User has no permission or article is already category
        if ($article->isStartArticle() || !rex::getUser()->hasPerm('article2category[]')) {
            return '';
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'rex-api-call' => 'article2category',
            'article_id' => $article_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('content_tocategory'),
            'icon' => 'rex-icon rex-icon-category',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
                'title' => rex_i18n::msg('content_tocategory'),
                'data-confirm' => rex_i18n::msg('content_tocategory').'?',
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
