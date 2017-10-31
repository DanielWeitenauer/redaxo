<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_name_sortable extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $category = rex_article::get($this->edit_id)->getCategory();
        $article_order = $category->getValue('article_order');

        $return = rex_i18n::msg('header_article_name');

        if ($article_order != 'priority, name') {
            $url_params = [
                'rex-api-call' => 'article_name_sort',
                'category_id' => $category->getId(),
                'clang_id' => $this->clang,
            ];
            $return = '<a href="'.$this->context->getUrl($url_params).'">'.$return.'</a>';
        }

        return $return;
    }
}
