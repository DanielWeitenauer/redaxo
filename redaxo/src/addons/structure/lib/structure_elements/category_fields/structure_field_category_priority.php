<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_category_priority extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $sql = $this->getDataProvider()->getSql();
        $category_priority = $sql->getValue('catpriority');

        $button_params = [
            'label' => htmlspecialchars($category_priority),
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'data-title' => rex_i18n::msg('header_priority'),
            ],
        ];

        return $this->getFragment($button_params);
    }
}
