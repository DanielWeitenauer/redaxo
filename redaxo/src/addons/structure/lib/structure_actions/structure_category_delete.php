<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_delete extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $category_id = $this->getVar('edit_id');

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('delete'),
            'icon' => 'rex-icon rex-icon-delete',
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'title' => rex_i18n::msg('delete'),
                'data-confirm' => rex_i18n::msg('delete').'?',
            ],
        ];

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'rex-api-call' => 'category_delete',
                'category-id' => $category_id,
            ]);
            $button_params['url'] = $this->getVar('context')->getUrl($url_params, false);
            $button_params['attributes']['class'][] = 'btn-default';
        } else {
            $button_params['attributes']['class'][] = 'text-muted';
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
