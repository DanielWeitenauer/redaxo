<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_icon extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if ($this->edit_id == rex_article::getSiteStartArticleId()) {
            $class = ' rex-icon-sitestartarticle';
        } elseif ($this->sql->getValue('startarticle') == 1) {
            $class = ' rex-icon-startarticle';
        } else {
            $class = ' rex-icon-article';
        }

        $article_icon = '<i class="rex-icon'.$class.'"></i>';
        $article_title = htmlspecialchars($this->sql->getValue('name'));
        $category_id = rex_article::get($this->edit_id)->getCategoryId();

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $edit_url = $this->context->getUrl([
                'page' => 'content/edit',
                'article_id' => $this->edit_id,
                'mode' => 'edit'
            ]);

            $article_icon = '<a href="'.$edit_url.'" title="'.$article_title.'">'.$article_icon.'</a>';
        }

        return $article_icon;
    }
}
