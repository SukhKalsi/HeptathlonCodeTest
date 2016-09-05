<?php

/**
 * Main atheltic body which stores all the
 * sporting and event types
 * and key values for calculating each type of event
 *
 * This class should not be used directly. 
 * Each sport type must extend this.
 */
class IAAF {
    
    /**
     * Public constants
     * @var string
     */
    const SPORT_TYPE_HEPTATHLON = 'heptathlon';
    const EVENT_TYPE_RUNNING = 'running';
    const EVENT_TYPE_JUMPING = 'jumping';
    const EVENT_TYPE_THROWING = 'throwing';

    /**
     * Key value store of the three types of weighting for each event type
     * @var array
     */
    private $weightings = [
        '200m' => [
            "A" => 4.99087,
            "B" => 42.5,
            "C" => 1.81,
            "eventType" => self::EVENT_TYPE_RUNNING
        ],
        '800m' => [
            "A" => 0.11193,
            "B" => 254,
            "C" => 1.88,
            "eventType" => self::EVENT_TYPE_RUNNING
        ],
        '100m' => [
            "A" => 9.23076,
            "B" => 26.7,
            "C" => 1.835,
            "eventType" => self::EVENT_TYPE_RUNNING
        ],
        'high' => [
            "A" => 1.84523,
            "B" => 75.0,
            "C" => 1.348,
            "eventType" => self::EVENT_TYPE_JUMPING
        ],
        'long' => [
            "A" => 0.188807,
            "B" => 210,
            "C" => 1.41,
            "eventType" => self::EVENT_TYPE_JUMPING
        ],
        'shot' => [
            "A" => 56.0211,
            "B" => 1.50,
            "C" => 1.05,
            "eventType" => self::EVENT_TYPE_THROWING
        ],
        'javelin' => [
            "A" => 15.9803,
            "B" => 3.80,
            "C" => 1.04,
            "eventType" => self::EVENT_TYPE_THROWING
        ]
    ];

    /**
     * Getter for weighting type
     * @param  string $abbreviation
     * @param  string $forType
     * @return string
     */
    protected function getWeighting($abbreviation, $forType) {
        return $this->weightings[$abbreviation][$forType];
    }

    /**
     * Getter for event type
     * @param  string $abbreviation
     * @return string
     */
    protected function getEvent($abbreviation) {
        return $this->weightings[$abbreviation]["eventType"];
    }
}
