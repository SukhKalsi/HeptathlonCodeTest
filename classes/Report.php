<?php

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