<?php
/**
 * Utility class
 */
class Helper {
    public static function parseCsv($filename) {
        return array_map('str_getcsv', file($filename));
    }

    // public static function printTableRow($message = '') {
        // print('<tr>' . $message . '</tr>');
    // }

    public static function printLn($message = '') {
        // print($message . '<br />');
        print($message . PHP_EOL);
    }
}

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

/**
 * Reporting class which handles the final output to the screen
 */
class Report {
    
    private $data;
    private $results = array();
    private $type;

    /**
     * Upon construction ensure we can parse the given file
     * @param string $filename Relative path to the file
     * @param string $type     sporting event. See IAAF for available SPORT_TYPE_{xyz}
     */
    public function __construct($filename, $type) {
        $this->data = Helper::parseCsv($filename);
        $this->type = $type;
    }

    /**
     * Sort the scores in descending order
     * @param  Athlete $a
     * @param  Athlete $b
     * @return int
     */
    private function sort(Athlete $a, Athlete $b) {
        if ($a->getScore() == $b->getScore()) {
            return 0;
        }
        return ($a->getScore() > $b->getScore()) ? -1 : 1;
    }

    /**
     * Main function
     * Determines what the processed results are
     * Ready for the output function to execute further 
     * @return void
     */
    public function process() {

        if (empty($this->data)) {
            return;
        }

        // Iterate over the data and process each item individually
        array_walk($this->data, function($item) {
            $this->processItem($item);
        });
    }

    /**
     * Process each item in the data set
     * Inserts new or cumulative athlete score to result set
     * @param  [type] $item [description]
     * @return [type]       [description]
     */
    private function processItem($item) {
        $name = trim(strtolower($item[0]));
        $abbreviation = trim(strtolower($item[1]));
        $value = trim($item[2]);
        $date = trim($item[3]);
        $dateKey = DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d M Y');
        
        $this->addResultKeys($dateKey, $name);
        $athlete = $this->results[$dateKey][$name];
        $this->addScoreForAthlete($athlete, $abbreviation, $value);
    }

    /**
     * Inserts new date and/or athlete to result set
     * @param string $dateKey
     * @param string $name
     */
    private function addResultKeys($dateKey, $name) {
        if (!array_key_exists($dateKey, $this->results)) {
            $this->results[$dateKey] = [];
        }

        if (!array_key_exists($name, $this->results[$dateKey])) {
            $this->results[$dateKey][$name] = new Athlete($name);
        }
    }

    /**
     * Detects sport type and updates Athlete score within result set
     * @param Athlete $athlete
     * @param string  $abbreviation
     * @param string  $value
     */
    private function addScoreForAthlete(Athlete $athlete, $abbreviation, $value) {
        switch ($this->type) {
            case IAAF::SPORT_TYPE_HEPTATHLON:
                $sport = new Heptathlon();
                $score = $sport->calculateScore($abbreviation, $value);
                break;
            
            default:
                throw new Exception("Error Processing Request. Unidentified type " . $this->type, 1);
                break;
        }

        $athlete->addScore($score);
    }

    /**
     * Second main function
     * Outputs HTML table if there are results
     * Otherwise prints out default message.
     * @return [type] [description]
     */
    public function output() {
        if (empty($this->results)) {
            die('No results.');
        }

        // print('<table><tbody>');

        $day = 1;
        foreach ($this->results as $date => $results) {
            $title = 'Day ' . $day . ': ' . $date;
            // Helper::printLn('<th>' . $title . '</th>');
            Helper::printLn($title);
            
            uasort($results, array($this, 'sort'));

            foreach ($results as $key => $value) {
                $name = $value->getName();
                $score = $value->getScore();
                $spaceCount = 20 - strlen($score);
                $athlete = str_pad($name, $spaceCount, ' ');
                $output = strtoupper($athlete) . $score;
                // Helper::printLn('<td>' . $scorer . '</td>');
                Helper::printLn($output);
            }

            Helper::printLn();
            $day += 1;
        }

        // print('</tbody></table>');
    }
}

// small test before executing program
$test = new Heptathlon();
$testScore = $test->calculateScore('100m', 16.2);
if ($testScore !== 690) {
    die('Unit test failed. Expected 690 (int). Actual ' . $testScore . ' ' . gettype($testScore));
}

// Run
try {
    $t = new Report('Heptathlon.csv', IAAF::SPORT_TYPE_HEPTATHLON);
    $t->process();
    $t->output();
} catch (Exception $e) {
    echo 'Failed execution.' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}

?>