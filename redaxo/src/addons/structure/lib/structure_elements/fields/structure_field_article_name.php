<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_name extends rex_structure_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function getField()
    {
        $article_id = $this->getVar('edit_id');
        $category_id = rex_article::get($article_id)->getCategoryId();
        $sql = $this->getSql();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $field_params = [
            'label' => htmlspecialchars($sql->getValue('name')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        // Active state
        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'page' => 'content/edit',
                'article_id' => $article_id,
                'mode' => 'edit'

            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
        }
        // Inactive state
        else {
            $field_params['attributes']['class'][] = 'disabled';
        }

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
