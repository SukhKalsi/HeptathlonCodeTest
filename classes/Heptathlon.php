<?php

class Heptathlon extends IAAF {
    private $data;
    
    /**
     * Specifically for this type of sport
     * Calculate the score based of IAAF formular.
     * This sport type has multiple event types
     * @param  string $type  
     * @param  string $value
     * @return int
     */
    public function calculateScore($type, $value) {
        $weightA = $this->getWeighting($type, "A");
        $weightB = $this->getWeighting($type, "B");
        $weightC = $this->getWeighting($type, "C");

        $eventType = $this->getEvent($type);

        /**
         * Calculate the score based on IAAF standards
         * 
         * For running events: P = A(B-T)^C
         * For throwing events: P = A(D-B)^C
         * For jumping events: P = A(M-B)^C

         * Where:
         * P is the number of points scored for the event in question by an athlete.
         * M is the measurement (in centimetres) for jumps.
         * D is the distance (in metres) achieved in a throwing event.
         * T is the time (in seconds) for running events.
         */
        switch ($eventType) {
            case self::EVENT_TYPE_RUNNING:
                // running should be in seconds
                $time = explode(':', $value);
                if (count($time) === 2) {
                    $minToSec = $time[0] * 60;
                    $value = $minToSec + $time[1];
                }

                $score = $weightA * pow(($weightB - floatval($value)), $weightC);
                break;

            case self::EVENT_TYPE_JUMPING:
                // jumps should be in centimetres
                // some records have just value - blindly assuming this is correct
                // unless it explicity states 'm' for metres
                if (substr($value, -1) === 'm') {
                    $value = floatval($value) * 100;
                } else {
                    $value = floatval($value);
                }

                $score = $weightA * pow(($value - $weightB), $weightC);
                break;

            case self::EVENT_TYPE_THROWING:
                // throws should be in metres
                // some records have just value - blindly assuming this is correct
                // unless it explicity states 'cm' for centimetres
                if (substr($value, -2) === 'cm') {
                    $value = floatval($value) / 100;
                } else {
                    $value = floatval($value);
                }

                $score = $weightA * pow(($value - $weightB), $weightC);
                break;
            
            default:
                throw new Exception("Error Processing Request - unidentified event type for score.", 1);
                break;
        }

        return (int)floor($score);
    }

}