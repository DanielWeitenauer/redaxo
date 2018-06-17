<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_category_id extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $sql = $this->getDataProvider()->getSql();
        $active_category_id = $sql->getValue('id');

        $button_params = [
            'label' => htmlspecialchars($active_category_id),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        return $this->getFragment($button_params);
    }
}
