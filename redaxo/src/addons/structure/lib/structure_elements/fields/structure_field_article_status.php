<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_status extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $edit_id = $this->getDataProvider()->getEditId();
        $article = rex_article::get($edit_id);
        $category_id = $article->getCategoryId();
        $user = rex::getUser();

        $status_key = $article->getValue('status');
        $status_types = rex_article_service::statusTypes();
        $status_name = $status_types[$status_key][0];
        $status_class = $status_types[$status_key][1];
        $status_icon = $status_types[$status_key][2];

        $field_params = [
            'label' => $status_name,
            'hidden_label' => $this->isHiddenLabel(),
            'icon' => 'rex-icon '.$status_icon,
            'attributes' => [
                'class' => [
                    'btn',
                    $status_class,
                ],
                'title' => $status_name,
            ],
        ];

        // Active state
        if (!$article->isStartArticle() && ($user->hasPerm('publishArticle[]') || $user->getComplexPerm('structure')->hasCategoryPerm($category_id))) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'rex-api-call' => 'article_status',
                'article_id' => $edit_id,
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
