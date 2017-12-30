<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_name extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $category_id = $this->getVar('edit_id');

        $button_params = [
            'label' => htmlspecialchars($this->getVar('sql')->getValue('name')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        // Edit link
        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'category_id' => $category_id,
            ]);
            $button_params['url'] = $this->getVar('context')->getUrl($url_params, false);
        }

        return $this->getButtonFragment($button_params);
    }
}
