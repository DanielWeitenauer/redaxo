<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_delete extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $edit_id = $this->getDataProvider()->getEditId();

        $sql = $this->getDataProvider()->getSql();

        $category_permission = $this->getDataProvider()->getCategoryPermission();

        $field_params = [
            'label' => rex_i18n::msg('delete'),
            'hidden_label' => $this->isHiddenLabel(),
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
        if ($sql->getValue('startarticle') != 1 && $category_permission) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'article_id' => $edit_id,
                rex_api_article_delete::getUrlParams(),
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
