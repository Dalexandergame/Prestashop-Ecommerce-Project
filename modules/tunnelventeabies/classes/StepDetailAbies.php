<?php

class StepDetailAbies {

    private $titre;
    private $active = false;
    private $url;

    function __construct($titre = "", $url = "", $active = false) {
        $this->titre = $titre;
        $this->active = $active;
        $this->url = $url;
    }

    function getTitre() {
        return $this->titre;
    }

    function getActive() {
        return $this->active;
    }

    function getUrl() {
        $bUrl = _PS_BASE_URL_ . __PS_BASE_URI__ . "module/tunnelventeabies/";
        return $bUrl.$this->url;
    }

    /**
     * 
     * @param type $titre
     * @return \StepDetail
     */
    function setTitre($titre) {
        $this->titre = $titre;
        return $this;
    }

    /**
     * 
     * @param type $active
     * @return \StepDetail
     */
    function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * 
     * @param type $url
     * @return \StepDetail
     */
    function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function __toString() {
        return get_class($this) . "{$this->getTitre()}-{$this->getUrl()}";
    }

}
