<?php
namespace App;

use Illuminate\Support\Facades\Cookie;

class GeoMemo
{
    const EARTH_RADIUS = 6371;
    const COOKIE_NAME = 'geomemo';

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
        $sql = '
            INSERT INTO place (
                lat, lng, label, note, posx, posy, posz, last_modified_at
            ) VALUES (:lat, :lng, :label, :note, :posx, :posy, :posz, :last_modified_at)
        ';

        $coordinates = self::spherical2cartesian($lat, $lng);

        \DB::insert($sql, [
            'lat' => $lat,
            'lng' => $lng,
            'label' => $label,
            'note' => $note,
            'posx' => $coordinates['x'],
            'posy' => $coordinates['y'],
            'posz' => $coordinates['z'],
            'last_modified_at' => time()
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
        $coordinates = self::spherical2cartesian($lat, $lng);

        $sql = 'SELECT * FROM place';

        $rows = Util::object2array(\DB::select($sql));
        foreach ($rows as &$row) {
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
        $sql = '
            UPDATE place set
                lat = :lat,
                lng = :lng,
                label = :label,
                note = :note,
                posx = :posx,
                posy = :posy,
                posz = :posz,
                last_modified_at = :last_modified_at
            WHERE place_id = :place_id
        ';

        $coordinates = self::spherical2cartesian($lat, $lng);

        \DB::update($sql, [
            'lat' => $lat,
            'lng' => $lng,
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
        \DB::delete(
            'DELETE from place WHERE place_id = :place_id',
            ['place_id' => $placeId]
        );
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
