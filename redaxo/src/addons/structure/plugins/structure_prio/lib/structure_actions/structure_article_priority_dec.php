<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_priority_dec extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        if (!$article instanceof rex_article) {
            return '';
        }

        $category_id = $article->getCategoryId();
        /** @var rex_sql $sql */
        $sql = $this->getVar('sql');
        /** @var rex_context $context */
        $context = $this->getVar('context');

        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            return '';
        }

        $old_priority = $sql->getValue('priority');
        $new_priority = $old_priority - 1;
        if ($new_priority < 1) {
            $new_priority = 1;
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'rex-api-call' => 'article_priority',
            'article_id' => $article_id,
            'old_priority' => $old_priority,
            'new_priority' => $new_priority,
        ]);

        $button_params = [
            'hidden_label' => rex_i18n::msg('up'),
            'url' => $context->getUrl($url_params, false),
            'icon' => 'fa fa-arrow-up',
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
            ],
        ];

        return $this->getButtonFragment($button_params);
    }

    public function getModal()
    {
        return '';
    }
}
