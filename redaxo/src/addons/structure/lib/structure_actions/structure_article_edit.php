<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_edit extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($this->edit_id)) {
            return '';
        }

        $url_params = array_merge($this->url_params, [
            'form_article_edit' => $this->edit_id,
        ]);

        $return = '<a href="'.$this->context->getUrl($url_params).'" class="btn btn-default"><i class="rex-icon rex-icon-edit"></i></a>';

        // Display form if necessary
        if (rex_request('form_article_edit', 'int', -1) == $this->edit_id) {
            echo $this->getModal();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getModal()
    {
        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($this->edit_id)) {
            return '';
        }

        $article = rex_article::get($this->edit_id);
        $category_id = $article->getCategoryId();

        $template_select = '';
        if (rex_addon::get('structure')->getPlugin('content')->isAvailable()) {
            $select = new rex_template_select();
            $select->setName('template_id');
            $select->setSize(1);
            $select->setStyle('class="form-control"');
            $select->setSelected($article->getValue('template_id'));

            $template_select = '
                <dl class="dl-horizontal text-left">
                    <dt><label for="article-name">'.rex_i18n::msg('header_template').'</label></dt>
                    <dd>'.$select->get().'</dd>
                </dl>
            ';
        }

        $url_params = array_merge($this->url_params, [
            'category_id' => $category_id,
            'article_id' => $this->edit_id,
        ]);

        return '  
            <div class="modal fade" id="article-edit-'.$this->edit_id.'">
                <div class="modal-dialog">
                    <form id="rex-form-article-edit-'.$this->edit_id.'" class="modal-content form-horizontal" action="'.$this->context->getUrl($url_params).'" method="post" enctype="multipart/form-data" data-pjax-container="#rex-page-main">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title">'.rex_i18n::msg('article_edit').'</h3>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="rex-api-call" value="article_edit" />
                            <dl class="dl-horizontal text-left">
                                <dt>'.rex_i18n::msg('header_id').'</dt>
                                <dd>'.$this->edit_id.'</dd>
                            </dl>
                            <dl class="dl-horizontal text-left">
                                <dt><label for="article-name">'.rex_i18n::msg('header_article_name').'</label></dt>
                                <dd><input class="form-control" type="text" name="article-name" value="'.htmlspecialchars($article->getName()).'" autofocus /></dd>
                            </dl>
                            '.$template_select.'
                            <dl class="dl-horizontal text-left">
                                <dt>'.rex_i18n::msg('header_date').'</dt>
                                <dd>'.rex_formatter::strftime($article->getCreateDate(), 'date').'</dd>
                            </dl>
                            <dl class="dl-horizontal text-left">
                                <dt><label for="article-position">'.rex_i18n::msg('header_priority').'</label></dt>
                                <dd><input class="form-control" type="text" name="article-position" value="'.htmlspecialchars($article->getPriority()).'" /></dd>
                            </dl>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-send" type="submit" name="artedit_function" '.rex::getAccesskey(rex_i18n::msg('article_save'), 'save').'>'.rex_i18n::msg('article_save').'</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">'.rex_i18n::msg('form_abort').'</button>
                        </div>
                    </form>
                </div>
            </div> 
            <script>
                $(document).ready(function() {
                    $("#article-edit-'.$this->edit_id.'").modal();
                });
            </script>
        ';
    }
}
