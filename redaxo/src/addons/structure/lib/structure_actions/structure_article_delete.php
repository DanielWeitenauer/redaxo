<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_delete extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (rex_article::get($this->edit_id)->isStartArticle()) {
            return '';
        }

        $category_id = rex_article::get($this->edit_id)->getCategoryId();

        $url_params = array_merge($this->url_params, [
            'page' => 'structure',
            'rex-api-call' => 'article_delete',
            'article_id' => $this->edit_id,
            'category_id' => $category_id,
        ]);

        return '<a class="btn btn-default" href="'.$this->context->getUrl($url_params).'" data-confirm="'.rex_i18n::msg('delete').'?" title="'.rex_i18n::msg('delete').'"><i class="rex-icon rex-icon-delete"></i></a>';
    }
}
