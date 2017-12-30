<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_name extends rex_structure_action_field
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
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $button_params = [
            'label' => htmlspecialchars($sql->getValue('name')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $url_params = array_merge($this->getVar('url_params'), [
                'page' => 'content/edit',
                'article_id' => $article_id,
                'mode' => 'edit'

            ]);
            $button_params['url'] = $context->getUrl($url_params, false);
        }

        return $this->getButtonFragment($button_params);
    }
}
