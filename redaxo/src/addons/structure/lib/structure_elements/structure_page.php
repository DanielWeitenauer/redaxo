<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2018 studio ahoi
 */

class rex_structure_page
{
    /**
     * Traits
     */
    use rex_factory_trait;
    use rex_structure_trait_vars;

    /**
     * @var array
     */
    protected $blocks = [];

    /**
     * @param array $vars
     *
     * @return static
     */
    public static function factory($vars = [])
    {
        $class = static::getFactoryClass();

        return new $class($vars);
    }

    /**
     * @param array $vars
     */
    protected function __construct($vars = [])
    {
        $this->setVars($vars);
    }
}
