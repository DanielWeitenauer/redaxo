<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article2startarticle extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        // User has no permission or article is in root
        if ($article->isStartArticle() || !$article->getParentId() || !$user->hasPerm('article2startarticle[]')) {
            return '';
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'rex-api-call' => 'article2startarticle',
            'category_id' => $article_id, // As the active category id also changes, set new target
            'article_id' => $article_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('content_tostartarticle'),
            'icon' => 'rex-icon rex-icon-startarticle',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
                'title' => rex_i18n::msg('content_tostartarticle'),
                'data-confirm' => rex_i18n::msg('content_tostartarticle').'?',
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
