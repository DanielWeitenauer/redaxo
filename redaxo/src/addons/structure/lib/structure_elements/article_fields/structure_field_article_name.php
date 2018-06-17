<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_name extends rex_structure_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function getField()
    {
        $edit_id = $this->getDataProvider()->getEditId();
        $category_id = rex_article::get($edit_id)->getCategoryId();
        $sql = $this->getDataProvider()->getSql();

        $field_params = [
            'label' => htmlspecialchars($sql->getValue('name')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        // Active state
        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'page' => 'content/edit',
                'article_id' => $edit_id,
                'mode' => 'edit'
            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
        }
        // Inactive state
        else {
            $field_params['attributes']['class'][] = 'disabled';
        }

        return $this->getFragment($field_params);
    }
}
