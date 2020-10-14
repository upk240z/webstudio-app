<?php
namespace App;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Cookie;

class GeoMemo
{
    const EARTH_RADIUS = 6371;
    const COOKIE_NAME = 'geomemo';

    /**
     * @var FirestoreClient null
     */
    private static $firestore = null;

    private static function connect()
    {
        if (self::$firestore == null) {
            self::$firestore = new FirestoreClient();
        }
    }

    private static function id($id)
    {
        return sprintf('%05d', $id);
    }

    private static function createId()
    {
        self::connect();

        $id = 1;
        $docs = self::$firestore->collection('place')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->documents();
        if (!$docs->isEmpty()) {
            foreach ($docs as $doc) {
                $id = (int)$doc['id'] + 1;
                break;
            }
        }

        return self::id($id);
    }

    public static function saveCookie($value)
    {
        Cookie::queue(Cookie::make(
            self::COOKIE_NAME,
            json_encode($value),
            60 * 24 * 30,
            '/'
        ));
    }

    public static function getCookie()
    {
        $json = Cookie::get(self::COOKIE_NAME);
        return $json == null ? null : json_decode($json, true);
    }

    public static function addPlace($lat, $lng, $label, $note)
    {
        self::connect();

        $id = self::createId();
        $coordinates = self::spherical2cartesian($lat, $lng);

        self::$firestore->collection('place')
            ->document($id)
            ->set([
                'id' => $id,
                'lat' => (float)$lat,
                'lng' => (float)$lng,
                'label' => $label,
                'note' => $note,
                'posx' => $coordinates['x'],
                'posy' => $coordinates['y'],
                'posz' => $coordinates['z'],
                'last_modified_at' => time(),
            ]);
    }

    private static function spherical2cartesian($lat, $lng)
    {
        $lat = deg2rad($lat);
        $lng = deg2rad($lng);
        return [
            'x' => self::EARTH_RADIUS * cos($lat) * cos($lng),
            'y' => self::EARTH_RADIUS * sin($lat),
            'z' => self::EARTH_RADIUS * cos($lat) * sin($lng)
        ];
    }

    public static function getPlaces($lat, $lng)
    {
        self::connect();

        $coordinates = self::spherical2cartesian($lat, $lng);

        $docs = self::$firestore->collection('place')->documents()->rows();
        $rows = [];
        foreach ($docs as $doc) {
            $row = $doc->data();
            $row['place_id'] = $row['id'];
            $distance = acos(
                (
                    $coordinates['x'] * $row['posx'] +
                    $coordinates['y'] * $row['posy'] +
                    $coordinates['z'] * $row['posz']
                ) / (self::EARTH_RADIUS ** 2)
            ) * self::EARTH_RADIUS;
            if (is_nan($distance)) {
                $distance = 0.0;
            }
            $row['distance'] = sprintf('%.2f', $distance);
            $rows[] = $row;
        }

        usort($rows, function($a, $b)
        {
            if ($a['distance'] == $b['distance']) {
                return 0;
            }
            return ($a['distance'] < $b['distance']) ? -1 : 1;
        });

        return $rows;
    }

    public static function updatePlace($placeId, $lat, $lng, $label, $note)
    {
        self::connect();

        $id = self::id($placeId);
        $coordinates = self::spherical2cartesian($lat, $lng);

        self::$firestore->collection('place')->document($id)
            ->set([
                'id' => $id,
                'lat' => (float)$lat,
                'lng' => (float)$lng,
                'label' => $label,
                'note' => $note,
                'place_id' => $placeId,
                'posx' => $coordinates['x'],
                'posy' => $coordinates['y'],
                'posz' => $coordinates['z'],
                'last_modified_at' => time()
            ]);
    }

    public static function removePlace($placeId)
    {
        self::connect();
        $id = self::id($placeId);
        self::$firestore->collection('place')->document($id)->delete();
    }

    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $pos1 = self::spherical2cartesian($lat1, $lng1);
        $pos2 = self::spherical2cartesian($lat2, $lng2);

        return acos(
            ($pos1['x'] * $pos2['x'] + $pos1['y'] * $pos2['y'] + $pos1['z'] * $pos2['z'])
            / (self::EARTH_RADIUS ** 2)
        ) * self::EARTH_RADIUS;
    }
}
