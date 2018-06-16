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
        $sql = $this->getSql();

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

    /**
     * @return rex_sql
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
