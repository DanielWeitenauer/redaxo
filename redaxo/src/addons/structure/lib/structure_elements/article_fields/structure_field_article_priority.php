<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_priority extends rex_structure_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function getField()
    {
        $sql = $this->getDataProvider()->getSql();

        $button_params = [
            'label' => htmlspecialchars($sql->getValue('priority')),
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
