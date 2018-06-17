<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2018 studio ahoi
 */

class rex_structure_data_provider
{
    /**
     * Traits
     */
    use rex_factory_trait;

    /**
     * @var int
     */
    protected $category_id;
    /**
     * @var int
     */
    protected $article_id;
    /**
     * @var int
     */
    protected $clang_id;
    /**
     * @var int
     */
    protected $ctype_id;
    /**
     * @var int
     */
    protected $artstart;
    /**
     * @var int
     */
    protected $catstart;
    /**
     * @var int
     */
    protected $edit_id;
    /**
     * @var string
     */
    protected $function;
    /**
     * @var array
     */
    protected $mountpoints;
    /**
     * @var rex_context
     */
    protected $context;
    /**
     * @var rex_sql
     */
    protected $sql;

    /**
     * @return static
     */
    public static function factory()
    {
        $class = static::getFactoryClass();

        return new $class();
    }

    /**
     * Use factory() method to instantiate
     */
    protected function __construct()
    {
    }

    /**
     * @param int $category_id
     *
     * @return $this
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = (int) $category_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        if (!isset($this->category_id)) {
            $this->category_id = rex_request('category_id', 'int');
            $this->category_id = rex_category::get($this->category_id) ? $this->category_id : 0;

            // Nur ein Mointpoint -> Sprung in die Kategory
            $mountpoints = $this->getMountpoints();
            if (count($mountpoints) == 1 && $this->category_id == 0) {
                $this->category_id = current($mountpoints);
            }
        }

        return $this->category_id;
    }

    /**
     * @param int $article_id
     *
     * @return $this
     */
    public function setArticleId($article_id)
    {
        $this->article_id = (int) $article_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getArticleId()
    {
        if (!isset($this->article_id)) {
            $this->article_id = rex_request('article_id', 'int');
            $this->article_id = rex_article::get($this->article_id) ? $this->article_id : 0;
        }

        return $this->article_id;
    }

    /**
     * @param int $edit_id
     *
     * @return $this
     */
    public function setEditId($edit_id)
    {
        $this->edit_id = (int) $edit_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getEditId()
    {
        if (!isset($this->edit_id)) {
            $this->edit_id = rex_request('edit_id', 'int');
        }

        return $this->edit_id;
    }

    /**
     * @param int $clang_id
     *
     * @return $this
     */
    public function setClangId($clang_id)
    {
        $this->clang_id = (int) $clang_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getClangId()
    {
        if (!isset($this->clang_id)) {
            $this->clang_id = rex_request('clang', 'int');
            $this->clang_id = rex_clang::exists($this->clang_id) ? $this->clang_id : rex_clang::getStartId();

            $stop = false;
            if (rex_clang::count() > 1) {
                if (!rex::getUser()->getComplexPerm('clang')->hasPerm($this->clang_id)) {
                    $stop = true;
                    foreach (rex_clang::getAllIds() as $key) {
                        if (rex::getUser()->getComplexPerm('clang')->hasPerm($key)) {
                            $this->clang_id = $key;
                            $stop = false;
                            break;
                        }
                    }

                    if ($stop) {
                        echo rex_view::error('You have no permission to this area');
                        exit;
                    }
                }
            } else {
                $this->clang_id = rex_clang::getStartId();
            }
        }

        return $this->clang_id;
    }

    /**
     * @param int $ctype_id
     *
     * @return $this
     */
    public function setCtypeId($ctype_id)
    {
        $this->ctype_id = (int) $ctype_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getCtypeId()
    {
        if (!isset($this->ctype_id)) {
            $this->ctype_id = rex_request('ctype', 'int');
        }

        return $this->ctype_id;
    }

    /**
     * @param int $artstart
     *
     * @return $this
     */
    public function setArtstart($artstart)
    {
        $this->artstart = (int) $artstart;

        return $this;
    }

    /**
     * @return int
     */
    public function getArtStart()
    {
        if (!isset($this->artstart)) {
            $this->artstart = rex_request('artstart', 'int');
        }

        return $this->artstart;
    }

    /**
     * @param int $catstart
     *
     * @return $this
     */
    public function setCatstart($catstart)
    {
        $this->catstart = (int) $catstart;

        return $this;
    }

    /**
     * @return int
     */
    public function getCatStart()
    {
        if (!isset($this->catstart)) {
            $this->catstart = rex_request('catstart', 'int');
        }

        return $this->catstart;
    }

    /**
     * @param string $function
     *
     * @return $this
     */
    public function setFunction($function)
    {
        $this->function = (string) $function;

        return $this;
    }

    /**
     * @return string
     */
    public function getFunction()
    {
        if (!isset($this->function)) {
            $this->function = rex_request('function', 'string');
        }

        return $this->function;
    }

    /**
     * @param array $mountpoints
     *
     * @return $this
     */
    public function setMountpoints(array $mountpoints)
    {
        $this->mountpoints = $mountpoints;

        return $this;
    }

    /**
     * @return array
     */
    public function getMountpoints()
    {
        if (!isset($this->mountpoints)) {
            $this->mountpoints = rex::getUser()->getComplexPerm('structure')->getMountpoints();
        }

        return $this->mountpoints;
    }

    /**
     * @param rex_context $context
     *
     * @return $this
     */
    public function setContext(rex_context $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return rex_context
     */
    public function getContext()
    {
        if (!($this->context instanceof rex_context)) {
            $this->context = new rex_context([
                'page'        => 'structure',
                'category_id' => $this->getCategoryId(),
                'article_id'  => $this->getArticleId(),
                'clang'       => $this->getClangId(),
            ]);
        }

        return $this->context;
    }

    /**
     * @param rex_sql $sql
     *
     * @return $this
     */
    public function setSql(rex_sql $sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * @return rex_sql
     */
    public function getSql()
    {
        if (!($this->sql instanceof rex_sql)) {
            $this->sql = rex_sql::factory();
            $this->sql->setQuery('SELECT * FROM '.rex::getTable('article').' WHERE id = ?', [
                $this->getEditId()
            ]);
        }

        return $this->sql;
    }

    /**
     * @return array
     */
    public function getUrlParams()
    {
        return [
            'artstart' => $this->getArtStart(),
            'catstart' => $this->getCatStart(),
        ];
    }
}
