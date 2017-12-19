<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_header_updatedate_sortable extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $category = rex_article::get($this->edit_id)->getCategory();
        $article_order = $category->getValue('article_order');

        $return = rex_i18n::msg('header_update_date');

        if ($article_order != 'priority, name') {
            $url_params = [
                'rex-api-call' => 'article_updatedate_sort',
                'category_id' => $category->getId(),
                'clang_id' => $this->clang,
            ];
            $return = '<a href="'.$this->context->getUrl($url_params).'">'.$return.'</a>';
        }

        return $return;
    }
}
