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
        $category_id = $this->getDataProvider()->getCategoryId();

        $sql = $this->getDataProvider()->getSql();
        $category_active_id = $sql->getValue('id');

        $user = rex::getUser();
        $category_permission = $user->getComplexPerm('structure')->hasCategoryPerm($category_id);

        $status_key = $sql->getValue('status');
        $status_types = rex_category_service::statusTypes();
        $status_name = $status_types[$status_key][0];
        $status_class = $status_types[$status_key][1];
        $status_icon = $status_types[$status_key][2];

        $field_params = [
            'hidden_label' => $this->isHiddenLabel(),
            'label' => $status_name,
            'icon' => 'rex-icon '.$status_icon,
            'attributes' => [
                'class' => [
                    'btn',
                    $status_class,
                ],
                'title' => $status_name,
            ],
        ];

        if ($category_permission) {
            // Active state
            if ($category_permission && $user->hasPerm('publishCategory[]')) {
                $context = $this->getDataProvider()->getContext();
                $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                    'category-id' => $category_active_id,
                    rex_api_category_status::getUrlParams(),
                ]);
                $field_params['url'] = $context->getUrl($url_params, false);
                $field_params['attributes']['class'][] = 'btn-default';
            }
            // Inactive state
            else {
                $field_params['attributes']['class'][] = 'text-muted disabled';
            }
        } elseif ($user->getComplexPerm('structure')->hasCategoryPerm($category_active_id)) {
            $field_params['attributes']['class'][] = 'text-muted disabled';
        }

        return $this->getFragment($field_params);
    }
}
