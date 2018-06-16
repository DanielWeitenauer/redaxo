<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_template extends rex_structure_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function getField()
    {
        if (!rex_addon::get('structure')->getPlugin('content')->isAvailable()) {
            return '';
        }

        $article_id = $this->getVar('edit_id');
        $sql = $this->getSql();
        $template_id = $sql->getValue('template_id');
        $category_id = rex_article::get($article_id)->getCategoryId();
        $templates = $this->getTemplates($category_id);

        if (!isset($templates[$template_id])) {
            return '';
        }

        $field_params = [
            'label' => $templates[$template_id],
            'attributes' => [
                'title' => rex_i18n::msg('header_template'),
                'data-title' => rex_i18n::msg('header_template'),
                'class' => [
                    'btn',
                ]
            ],
        ];

        return $this->getFragment($field_params);
    }

    /**
     * @param int $category_id
     * @return array
     */
    protected function getTemplates($category_id)
    {
        $return = [];

        $templates = rex_template::getTemplatesForCategory($category_id);
        if (count($templates) > 0) {
            foreach ($templates as $t_id => $t_name) {
                $return[$t_id] = rex_i18n::translate($t_name);
            }
        }

        $return[0] = rex_i18n::msg('template_default_name');

        return $return;
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
