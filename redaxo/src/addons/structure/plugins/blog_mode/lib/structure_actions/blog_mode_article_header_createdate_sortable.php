<?php
/**
 * @package redaxo\structure
 */
class rex_blog_mode_article_header_createdate_sortable extends rex_structure_action_field
{
    /**
     * @return string
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $category = rex_article::get($article_id)->getCategory();
        $article_order = $category->getValue('article_order');
        /** @var rex_context $context */
        $context = $this->getVar('context');

        $return = rex_i18n::msg('header_create_date');

        if ($article_order != 'priority, name') {
            $url_params = [
                'rex-api-call' => 'article_createdate_sort',
                'category_id' => $category->getId(),
                'clang_id' => $this->getVar('clang'),
            ];
            $return = '<a href="'.$context->getUrl($url_params).'">'.$return.'</a>';
        }

        return $return;
    }
}
