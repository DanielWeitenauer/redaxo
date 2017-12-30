<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

trait rex_structure_trait_vars
{
    /**
     * @var array
     */
    protected $_vars = [];

    /**
     * @param array $vars
     *
     * @return $this
     */
    public function setVars($vars)
    {
        $this->_vars = $vars;

        return $this;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * @return bool
     */
    public function hasVars()
    {
        return !empty($this->_vars);
    }

    /**
     * @param string $var_name
     * @param mixed $value
     *
     * @return $this
     */
    public function setVar($var_name, $value)
    {
        $this->_vars[$var_name] = $value;

        return $this;
    }

    /**
     * @param string $var_name
     * @param null $default
     *
     * @return mixed|null
     */
    public function getVar($var_name, $default = null)
    {
        return $this->hasVar($var_name) ? $this->_vars[$var_name] : $default;
    }

    /**
     * @param string $var_name
     *
     * @return mixed|null
     */
    public function hasVar($var_name)
    {
        return isset($this->_vars[$var_name]);
    }
}
