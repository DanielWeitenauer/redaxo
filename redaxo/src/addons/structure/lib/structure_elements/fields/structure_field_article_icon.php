<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_icon extends rex_structure_field
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

        if ($article_id == rex_article::getSiteStartArticleId()) {
            $icon_class = 'rex-icon rex-icon-sitestartarticle';
        } elseif ($sql->getValue('startarticle') == 1) {
            $icon_class = 'rex-icon rex-icon-startarticle';
        } else {
            $icon_class = 'rex-icon rex-icon-article';
        }

        $field_params = [
            'attributes' => [
                'class' => [
                    'btn',
                    $icon_class,
                ],
                'title' => htmlspecialchars($sql->getValue('name')),
            ],
        ];

        // Active state
        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'page' => 'content/edit',
                'article_id' => $article_id,
                'mode' => 'edit'
            ]);
            $field_params['url'] = $this->getVar('context')->getUrl($url_params, false);
        }
        // Inactive state
        else {
            $field_params['attributes']['class'][] = 'text-muted disabled';
        }

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
