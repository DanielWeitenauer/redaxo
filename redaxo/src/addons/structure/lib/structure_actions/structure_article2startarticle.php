<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article2startarticle extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $article = rex_article::get($this->edit_id);

        // User has no permission or article is in root
        if ($article->isStartArticle() || !rex::getUser()->hasPerm('article2startarticle[]') || !$article->getParentId()) {
            return '';
        }

        $url_params = array_merge($this->url_params, [
            'rex-api-call' => 'article2startarticle',
            'category_id' => $this->edit_id, // As the active category id also changes, set new target
            'article_id' => $this->edit_id,
        ]);

        $button_params = [
            'button' => [
                'hidden_label' => rex_i18n::msg('content_tostartarticle'),
                'icon' => 'startarticle',
                'url' => $this->context->getUrl($url_params, false),
                'attributes' => [
                    'class' => [
                        'btn-default',
                    ],
                    'title' => rex_i18n::msg('content_tostartarticle'),
                    'data-confirm' => rex_i18n::msg('content_tostartarticle').'?',
                ],
            ]
        ];

        $this->setVar('buttons', $button_params);

        return $this->parse('core/buttons/button.php');
    }
}
