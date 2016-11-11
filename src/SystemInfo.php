<?php
namespace ShortCord;

require_once 'Helpers.php';

class SystemInfo {

	public function Report($drive): array {
            return [
                'date' => date("F j Y, H:i:s"),
                'uptime' => Helpers\Uptime(),
                'disk' => $this->getDiskInfo($drive),
                'ram' => $this->getRamInfo(),
                'temps' => $this->getTemps()['temps'],
                'fans' => $this->getTemps()['fans']
            ];
	}

    private function getTemps(): array {
        $data = '';
        $result = -1;
        exec('sh ' . dirname(__FILE__) . '/../bin/sensorBuild.sh', $data, $result);

        if ($result != 0) {
            return null;
        }

        // Decode the json return and auto convert stdClass to array
        $decodedPayload = json_decode($data[0], $assoc = true);
        //return only the values
        return $decodedPayload;
    }

	private function kb2bytes($kb): int {
		return round($kb * 1024, 2);
	}

	private function numbers_only($string): int {
        return preg_replace('/[^0-9]/', '', $string);
	}

	private function calculate_percentage($used, $total): float {
        return round(100 - $used / $total * 100, 0, PHP_ROUND_HALF_UP);
	}

	private function getDiskInfo(string $mount): array {
        $format = function($value) {
            return Helpers\format_bytes(round($value * 1024, 2));
        };
        
        //['5'] == filesystem; etc /dev/mapper/xxx
        //['4'] == used precentage
        //['3'] == 1k blocks
        //['2'] == used 
        //['1'] == total
        //['0'] == mount location; etc /
        
        $execResult = [];
        exec("df $mount", $execResult);

        // we get the count of entrys
        $lastIndex = count($execResult) - 1;

        // the last entry of $execResult /should/ be the data we need
        // we replace any continuous whitespace with a single whitespace
        // then we explode on any single whitespace
        $driveInfo = explode(' ', preg_replace('/\s\s+/', ' ', $execResult[$lastIndex]));

        return [
                'used' => $format($driveInfo[2]), //used
                'total' => $format($driveInfo[1]), //total
                'percent' => trim($driveInfo['4'], "%"), // used in precentage
        ];
	}

	private function getRamInfo(): array {

        $memory = [
            'Total RAM'  => 'MemTotal',
            'Free RAM'   => 'MemFree',
            'Cached RAM' => 'Cached',
            'Total Swap' => 'SwapTotal',
            'Free Swap'  => 'SwapFree'
        ];

        foreach ($memory as $key => $value) {
            $memory[$key] = $this->kb2bytes($this->numbers_only(exec('grep -E "^'.$value.'" /proc/meminfo')));
        }

        $memory['Used Swap'] = $memory['Total Swap'] - $memory['Free Swap'];
        $memory['Used RAM'] = $memory['Total RAM'] - $memory['Free RAM'] - $memory['Cached RAM'];
        $memory['RAM Percent Free'] = $this->calculate_percentage($memory['Used RAM'],$memory['Total RAM']);
        $memory['Swap Percent Free'] = $this->calculate_percentage($memory['Used Swap'],$memory['Total Swap']);

        return array(
            'used' => array(
                    'ram' => array(
                        'size' => Helpers\format_bytes($memory['Total RAM'] - $memory['Free RAM'] - $memory['Cached RAM']),
                        'percent' => $this->calculate_percentage($memory['Used RAM'],$memory['Total RAM']),
                    ),
                    'swap' => array(
                        'size' => Helpers\format_bytes($memory['Total Swap'] - $memory['Free Swap']),
                        'percent' => $this->calculate_percentage($memory['Free Swap'], $memory['Total Swap']),
                    ),
            ),
            'free' => array(
                    'ram' => array(
                        'size' => Helpers\format_bytes($memory['Free RAM']),
                        'percent' => 100 - $this->calculate_percentage($memory['Used RAM'],$memory['Total RAM']),
                    ),
                    'swap' => array(
                        'size' => Helpers\format_bytes($memory['Free Swap']),
                        'percent' => 100 - $this->calculate_percentage($memory['Free Swap'], $memory['Total Swap']),
                    ),
            ),
            'total' => array(
                'ram' => Helpers\format_bytes($memory['Total RAM']),
                'swap' => Helpers\format_bytes($memory['Total Swap']),
            ),
        );
	}

	
	private function get_network(): array {
        $vnstat = null;
        exec('/usr/bin/vnstat --json -i eth0', $vnstat);

        $vnstat = json_decode(implode($vnstat), true);

        $vnstat['interfaces'][0]['traffic']['total']['rx'] = Helpers\format_bytes($vnstat['interfaces'][0]['traffic']['total']['rx']);
        $vnstat['interfaces'][0]['traffic']['total']['tx'] = Helpers\format_bytes($vnstat['interfaces'][0]['traffic']['total']['tx']);

        $vnstat['interfaces'][0]['traffic']['days'][0]['rx'] = Helpers\format_bytes($vnstat['interfaces'][0]['traffic']['days'][0]['rx']);
        $vnstat['interfaces'][0]['traffic']['days'][0]['tx'] = Helpers\format_bytes($vnstat['interfaces'][0]['traffic']['days'][0]['tx']);

        $vnstat['interfaces'][0]['traffic']['months'][0]['rx'] = Helpers\format_bytes($vnstat['interfaces'][0]['traffic']['months'][0]['rx']);
        $vnstat['interfaces'][0]['traffic']['months'][0]['tx'] = Helpers\format_bytes($vnstat['interfaces'][0]['traffic']['months'][0]['tx']);

        return $vnstat;
	}
}
