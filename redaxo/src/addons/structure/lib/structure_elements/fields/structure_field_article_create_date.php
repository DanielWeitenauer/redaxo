<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_create_date extends rex_structure_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function getField()
    {
        $sql = $this->getSql();
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

    /**
     * @return rex_sql
     * @throws rex_sql_exception
     */
    protected function getSql()
    {
        if ($this->hasVar('sql') instanceof rex_sql) {
            return $this->getVar('sql');
        }

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM '.rex::getTable('article').' WHERE id = ?', [
            $this->getVar('edit_id')
        ]);

        return $sql;
    }
}
