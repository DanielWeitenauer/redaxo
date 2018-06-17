<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_id extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $sql = $this->getDataProvider()->getSql();

        $field_params = [
            'label' => htmlspecialchars($sql->getValue('id')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        return $this->getFragment($field_params);
    }
}
