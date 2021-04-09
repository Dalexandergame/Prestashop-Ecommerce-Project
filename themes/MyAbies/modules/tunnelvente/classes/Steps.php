<?php

class Steps {

    /**
     *
     * @var Steps 
     */
    private static $_instance;
    private $listStep = array();

    /**
     * Empêche la création externe d'instances.
     */
    private function __construct() {
        
    }

    /**
     * Empêche la copie externe de l'instance.
     */
    private function __clone() {
        
    }

    /**
     * Renvoi de l'instance et initialisation si nécessaire.
     * @return Steps
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * 
     * @param Step $step
     * @return \Steps
     */
    public function addStep(Step $step) {
        $step->setPosition(count($this->listStep));
        $this->listStep[$step->getPosition()] = $step;
        return $this;
    }

    /**
     * 
     * @return array Step
     */
    public function getListStep() {
        return $this->listStep;
    }

    /**
     * 
     * @param int $position
     * @return Step
     */
    public function getStepByPosition($position) {
        if (isset($this->listStep[$position - 1]))
            return $this->listStep[$position - 1];
        return NULL;
    }

    public function __toString() {
        return get_class($this) . "{$this->getTitre()}-{$this->getPosition()}";
    }

}
