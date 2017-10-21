<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article2category extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        // User has no permission or article is already category
        if (rex_article::get($this->edit_id)->isStartArticle() || !rex::getUser()->hasPerm('article2category[]')) {
            return '';
        }

        // Return button
        $url_params = array_merge($this->url_params, [
            'rex-api-call' => 'article2category',
            'article_id' => $this->edit_id,
        ]);

        $button_params = [
            'button' => [
                'hidden_label' => rex_i18n::msg('content_tocategory'),
                'icon' => 'category',
                'url' => $this->context->getUrl($url_params, false),
                'attributes' => [
                    'class' => [
                        'btn-default',
                    ],
                    'title' => rex_i18n::msg('content_tocategory'),
                    'data-confirm' => rex_i18n::msg('content_tocategory').'?',
                ],
            ]
        ];

        $this->setVar('buttons', $button_params);

        return $this->parse('core/buttons/button.php');
    }
}
