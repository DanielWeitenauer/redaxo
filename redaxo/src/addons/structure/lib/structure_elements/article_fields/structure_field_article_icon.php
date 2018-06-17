<?php
/**
 * @package redaxo\structure
 */
class rex_structure_field_article_icon extends rex_structure_field
{
    /**
     * @return string
     */
    public function getField()
    {
        $edit_id = $this->getDataProvider()->getEditId();

        $sql = $this->getDataProvider()->getSql();

        $category_permission = $this->getDataProvider()->getCategoryPermission();

        if ($edit_id == rex_article::getSiteStartArticleId()) {
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
        if ($category_permission) {
            $context = $this->getDataProvider()->getContext();
            $url_params = array_merge($this->getDataProvider()->getUrlParams(), [
                'page' => 'content/edit',
                'article_id' => $edit_id,
                'mode' => 'edit'
            ]);
            $field_params['url'] = $context->getUrl($url_params, false);
        }
        // Inactive state
        else {
            $field_params['attributes']['class'][] = 'text-muted disabled';
        }

        return $this->getFragment($field_params);
    }
}
