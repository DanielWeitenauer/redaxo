<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_status extends rex_structure_field
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

        $status_key = $article->getValue('status');
        $status_types = rex_article_service::statusTypes();
        $status_name = $status_types[$status_key][0];
        $status_class = $status_types[$status_key][1];
        $status_icon = $status_types[$status_key][2];

        $field_params = [
            $this->hasVar('hidden_label') && $this->getVar('hidden_label') ? 'hidden_label' : 'label' => $status_name,
            'icon' => 'rex-icon '.$status_icon,
            'attributes' => [
                'class' => [
                    $this->hasVar('class') ? $this->getVar('class') : 'btn',
                    $status_class,
                ],
                'title' => $status_name,
            ],
        ];

        // Active state
        if (!$article->isStartArticle() && ($user->hasPerm('publishArticle[]') || $user->getComplexPerm('structure')->hasCategoryPerm($category_id))) {
            $url_params = array_merge($this->getVar('url_params'), [
                'rex-api-call' => 'article_status',
                'article_id' => $article_id,
                rex_api_article_status::getUrlParams(),
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
