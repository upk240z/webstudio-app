<?php

namespace App;

class Util
{
    public static function trimSpace($str)
    {
        return mb_ereg_replace(
            '(\s|\n|\r|　)+$',
            '',
            mb_ereg_replace('^(\s|\n|\r|　)+', '', $str)
        );
    }

    public static function trimArray($params)
    {
        foreach ($params as $k => $v) {
            if (is_array($v)) {
                $params[$k] = self::trimArray($v);
            } else {
                $params[$k] = self::trimSpace($v);
            }
        }

        return $params;
    }

    public static function setMessage($name, $message)
    {
        $session = \Session::get('FLASH_MESSAGE');

        if (!is_array($session)) {
            $session = array();
        }

        if (!isset($session[$name])) {
            $session[$name] = array();
        }

        if (is_array($message)) {
            $session[$name] = array_merge($session[$name], $message);
        } else {
            $session[$name][] = $message;
        }

        \Session::put('FLASH_MESSAGE', $session);
    }

    public static function showMessage($name)
    {
        $session = \Session::get('FLASH_MESSAGE');
        if (is_array($session)) {
            if (isset($session[$name])) {
                $messages = $session[$name];
                echo \View::make('messages.' . $name, [
                    'messages' => $messages
                ]);
            }
            unset($session[$name]);
            \Session::put('FLASH_MESSAGE', $session);
        }
    }

    private static function bit2mask($bit)
    {
        $bit = intval($bit);
        return bindec(
            str_repeat('1', $bit) . str_repeat('0', 32 - $bit)
        );
    }

    public static function isIncludeIp($ip, $ranges)
    {
        if (is_array($ranges) == false)
        {
            $ranges = array($ranges);
        }

        $ipLong = ip2long($ip);

        foreach ($ranges as $range) {
            @list($rangeIp, $bit) = explode('/', $range);
            if (strlen($bit)) {
                $rangeIpLong = ip2long($rangeIp);
                $mask = self::bit2mask($bit);
                if (($ipLong & $mask) == ($rangeIpLong & $mask)) {
                    return true;
                }
            } else if ($ip == $range) {
                return true;
            }
        }

        return false;
    }

    public static function makePassword($len = 8)
    {
        $str = '2,3,4,5,6,7,8,9,2,3,4,5,6,7,8,9,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,J,K,L,M,N,P,Q,R,S,T,U,V,W,X,Y,Z';
        $arr = explode(',', $str);
        srand((double)microtime() * 1000000);
        $password = '';
        for($i=0; $i<$len; $i++ ) {
            $password .= $arr[ rand(0, count($arr) - 1) ];
        }

        return $password;
    }

    public static function getHolidays()
    {
        $url = 'https://calendar.google.com/calendar/ical/japanese__ja%40holiday.calendar.google.com/public/full.ics';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $ics = curl_exec($ch);
        curl_close($ch);

        $holidays = array(); $day = null;
        foreach (explode("\n", $ics) as $line) {
            $line = trim($line);
            if (substr($line, 0, 8) == 'DTSTART;') {
                $array = explode(':', $line);
                $day = $array[1];
            }
            if (substr($line, 0, 8) == 'SUMMARY:') {
                $array = explode(':', $line);
                $holidays[$day] = $array[1];
            }
        }

        ksort($holidays);

        return $holidays;
    }

    public static function translate($text, $from, $to)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, sprintf(
            "%s?appId=%s&text=%s&from=%s&to=%s",
            env('TRANSLATION_APP_URL'),
            env('TRANSLATION_APP_ID'),
            urlencode($text),
            urlencode($from),
            urlencode($to)
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return (string)simplexml_load_string($response);
    }

    public static function speak($text, $lang)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, sprintf(
            "%s?appId=%s&text=%s&language=%s",
            env('TRANSLATION_SPEAK_URL'),
            env('TRANSLATION_APP_ID'),
            urlencode($text),
            urlencode($lang)
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function createCsr($cn, $c, $st, $l, $o, $ou)
    {
        $fileKey = hash('sha256', uniqid('csr', true));
        $randFile = "/tmp/${fileKey}.dat";
        $keyFile = "/tmp/${fileKey}.key";
        $configFile = "/tmp/${fileKey}.config";

        $config = (string)\View::make('csr-config', [
            'cn' => $cn,
            'c' => $c,
            'st' => $st,
            'l' => $l,
            'o' => $o,
            'ou' => $ou,
        ]);
        file_put_contents($configFile, $config);

        exec('/usr/bin/openssl sha1 * > ' . $randFile);
        exec("/usr/bin/openssl genrsa -rand $randFile -out $keyFile 2048 2> /dev/null");
        exec("/usr/bin/openssl req -new -key $keyFile -config $configFile", $results);

        $key = file_get_contents($keyFile);
        $csr = trim(implode("\n", $results));

        if (strlen($csr) == 0) {
            throw new Exception('Failed to create CSR');
        }

        unlink($randFile);
        unlink($keyFile);
        unlink($configFile);

        return [
            'key' => $key,
            'csr' => $csr,
        ];
    }

    public static function object2array($data)
    {
        if (is_array($data)) {
            $array = [];
            foreach ($data as $object) {
                $array[] = (array)$object;
            }
            return $array;
        } else {
            return (array)$data;
        }
    }
}
