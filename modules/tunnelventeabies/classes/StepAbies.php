<?php

class StepAbies {

    private $titre;
    private $position;
    private $active = false;
    private $listStepDetail = array();

    function __construct($titre, $active = false) {
        $this->titre = $titre;
        $this->active = $active;
    }

    function getPosition() {
        return $this->position;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getActive() {
        return $this->active;
    }

    public function getListStepDetail() {
        return $this->listStepDetail;
    }

    /**
     * 
     * @param type $titre
     * @return \Step
     */
    public function setTitre($titre) {
        $this->titre = $titre;
        return $this;
    }

    function setPosition($position) {
        $this->position = $position;
    }

    /**
     * 
     * @param type $active
     * @return \Step
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * 
     * @param type $listStepDetail
     * @return \Step
     */
    public function setListStepDetail($listStepDetail) {
        $this->listStepDetail = $listStepDetail;
        return $this;
    }

    /**
     * 
     * @param StepDetail $stepDetail
     * @return \Step
     */
    public function addStepDetail(StepDetailAbies $stepDetail) {
        $this->listStepDetail[] = $stepDetail;
        return $this;
    }

    /**
     * 
     * @param int $position
     * @return StepDetail
     */
    public function getStepDetailByPosition($position) {
        if (isset($this->listStepDetail[$position - 1]))
            return $this->listStepDetail[$position - 1];
        return NULL;
    }

    public function __toString() {
        return get_class($this) . "{$this->getTitre()}-{$this->getPosition()}";
    }

}
