<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_status extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $category_id = $this->getVar('edit_id');
        $category = rex_category::get($category_id);
        $user = rex::getUser();

        $status_index = $category->getValue('status');
        $states = rex_category_service::statusTypes();
        $status = $states[$status_index][0];
        $status_class = $states[$status_index][1];
        $status_icon = $states[$status_index][2];


        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => $status,
            'icon' => 'rex-icon '.$status_icon,
            'attributes' => [
                'class' => [
                    'btn',
                    $status_class,
                ],
                'title' => $status,
            ],
        ];

        if ($user->hasPerm('publishCategory[]') || $user->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'rex-api-call' => 'category_status',
                'category-id' => $category_id,
            ]);
            $button_params['url'] = $this->getVar('context')->getUrl($url_params, false);
            $button_params['attributes']['class'][] = 'btn-default';
        } else {
            $button_params['attributes']['class'][] = 'text-muted';
        }

        return $this->getButtonFragment($button_params);
    }
}
