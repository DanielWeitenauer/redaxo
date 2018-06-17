<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_category_icon extends rex_structure_field
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

        $field_params = [
            'attributes' => [
                'class' => [
                    'btn',
                    'rex-icon rex-icon-category',
                ],
                'title' => htmlspecialchars($sql->getValue('catname')),
            ],
        ];

        if ($category_permission || $user->getComplexPerm('structure')->hasCategoryPerm($category_active_id)) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'category_id' => $category_active_id,
            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
        }

        return $this->getFragment($field_params);
    }
}
