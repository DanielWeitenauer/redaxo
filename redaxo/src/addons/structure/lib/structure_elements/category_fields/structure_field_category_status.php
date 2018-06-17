<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_category_status extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $edit_id = $this->getDataProvider()->getEditId();
        $category = rex_category::get($edit_id);
        $user = rex::getUser();

        $status_index = $category->getValue('status');
        $states = rex_category_service::statusTypes();
        $status = $states[$status_index][0];
        $status_class = $states[$status_index][1];
        $status_icon = $states[$status_index][2];


        $field_params = [
            'hidden_label' => $this->isHiddenLabel(),
            'label' => $status,
            'icon' => 'rex-icon '.$status_icon,
            'attributes' => [
                'class' => [
                    'btn',
                    $status_class,
                ],
                'title' => $status,
            ],
        ];

        // Active state
        if ($user->hasPerm('publishCategory[]') || $user->getComplexPerm('structure')->hasCategoryPerm($edit_id)) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'rex-api-call' => 'category_status',
                'category-id' => $edit_id,
                rex_api_category_status::getUrlParams(),
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
