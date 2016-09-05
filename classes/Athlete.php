<?php

/**
 * Athlete object to store individual data
 * This object should be instantiated for each day
 */
class Athlete {
    private $name;
    private $score = 0;

    public function __construct($name) {
        $this->name = $name;
    } 

    public function getName() {
        return $this->name;
    }

    public function getScore() {
        return $this->score;
    }

    public function addScore($value) {
        $this->score += $value;
    }
}
