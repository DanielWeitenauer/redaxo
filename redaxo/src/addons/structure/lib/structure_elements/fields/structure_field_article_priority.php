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
        $sql = $this->getSql();

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
