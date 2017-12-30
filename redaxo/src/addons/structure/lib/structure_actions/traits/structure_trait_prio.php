<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

trait rex_structure_trait_prio
{
    /**
     * @var int
     */
    protected $prio;

    /**
     * @param int $prio
     *
     * @return $this
     */
    public function setPrio($prio)
    {
        $this->prio = $prio;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrio()
    {
        return $this->prio;
    }
}
