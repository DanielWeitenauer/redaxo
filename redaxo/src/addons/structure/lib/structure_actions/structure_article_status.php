<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_status extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $category_id = $article->getCategoryId();
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $status_index = $article->getValue('status');
        $states = rex_article_service::statusTypes();
        $status = $states[$status_index][0];
        $status_class = $states[$status_index][1];
        $status_icon = $states[$status_index][2];

        $css_class = $this->hasVar('class') ? $this->getVar('class') : 'btn btn-default';

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => $status,
            'icon' => 'rex-icon '.$status_icon,
            'attributes' => [
                'class' => [
                    $css_class,
                    $status_class,
                ],
                'title' => $status,
            ],
        ];

        if (!$article->isStartArticle() && ($user->hasPerm('publishArticle[]') || $user->getComplexPerm('structure')->hasCategoryPerm($category_id))) {
            $url_params = array_merge($this->getVar('url_params'), [
                'rex-api-call' => 'article_status',
                'article_id' => $article_id,
            ]);
            $button_params['url'] = $context->getUrl($url_params, false);
        }

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
