<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_icon extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $category_id = rex_article::get($article_id)->getCategoryId();
        /** @var rex_sql $sql */
        $sql = $this->getVar('sql');

        if ($article_id == rex_article::getSiteStartArticleId()) {
            $icon_class = 'rex-icon rex-icon-sitestartarticle';
        } elseif ($sql->getValue('startarticle') == 1) {
            $icon_class = 'rex-icon rex-icon-startarticle';
        } else {
            $icon_class = 'rex-icon rex-icon-article';
        }

        $button_params = [
            'attributes' => [
                'class' => [
                    'btn',
                    $icon_class,
                ],
                'title' => htmlspecialchars($sql->getValue('name')),
            ],
        ];

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'page' => 'content/edit',
                'article_id' => $article_id,
                'mode' => 'edit'
            ]);
            $button_params['url'] = $this->getVar('context')->getUrl($url_params, false);
        }

        return $this->getButtonFragment($button_params);
    }

    /**
     * @return string
     */
    public function getModal()
    {
        return '';
    }
}
