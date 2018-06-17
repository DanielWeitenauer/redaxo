<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_create_date extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $sql = $this->getDataProvider()->getSql();
        $date = $sql->getDateTimeValue('createdate');

        $field_params = [
            'label' => rex_formatter::strftime($date, 'date'),
            'attributes' => [
                'data-title' => rex_i18n::msg('header_date'),
                'class' => [
                    'btn',
                ]
            ],
        ];

        return $this->getFragment($field_params);
    }
}
