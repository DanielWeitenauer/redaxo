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
        $edit_id = $this->getDataProvider()->getEditId();

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
        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($edit_id)) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'rex-api-call' => 'category_delete',
                'category-id' => $edit_id,
                rex_api_category_delete::getUrlParams(),
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
