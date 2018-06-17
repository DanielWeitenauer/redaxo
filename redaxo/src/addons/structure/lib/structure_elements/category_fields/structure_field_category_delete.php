<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_category_delete extends rex_structure_field
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
        $category_permission = $this->getDataProvider()->getCategoryPermission();

        $field_params = [
            'hidden_label' => $this->isHiddenLabel(),
            'label' => rex_i18n::msg('delete'),
            'icon' => 'rex-icon rex-icon-delete',
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'title' => rex_i18n::msg('delete'),
                'data-confirm' => rex_i18n::msg('delete').'?',
            ],
        ];

        // Active state
        if ($category_permission) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'category-id' => $category_active_id,
                rex_api_category_delete::getUrlParams(),
            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
            $field_params['attributes']['class'][] = 'btn-default';
        }
        // Inactive state
        elseif ($user->getComplexPerm('structure')->hasCategoryPerm($category_active_id)) {
            $field_params['attributes']['class'][] = 'text-muted disabled';
        }

        return $this->getFragment($field_params);
    }
}
