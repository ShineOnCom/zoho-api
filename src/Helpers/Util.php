<?php

namespace ShineOnCom\Zoho\Helpers;

use ShineOnCom\Zoho\Models\AbstractModel;

/**
 * Class Util
 */
class Util
{
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    public static function camel($value)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     */
    public static function flatten($array, $depth = INF)
    {
        return array_reduce($array, function ($result, $item) use ($depth) {
            if (! is_array($item)) {
                return array_merge($result, [$item]);
            } elseif ($depth === 1) {
                return array_merge($result, array_values($item));
            } else {
                return array_merge($result, static::flatten($item, $depth - 1));
            }
        }, []);
    }

    /**
     * @param int|string|array|\stdClass|\ShineOnCom\Zoho\Models\AbstractModel $mixed
     * @return int|null
     */
    public static function getKeyFromMixed($mixed)
    {
        if (is_numeric($mixed)) {
            return $mixed;
        } elseif (is_array($mixed) && isset($mixed['id'])) {
            return $mixed['id'];
        } elseif ($mixed instanceof \stdClass && isset($mixed->id)) {
            return $mixed->id;
        } elseif ($mixed instanceof AbstractModel) {
            return $mixed->getKey();
        } else {
            return null;
        }
    }

    /**
     * @return bool
     */
    public static function isLaravel()
    {
        return defined('LARAVEL_START') && ! static::isLumen();
    }

    /**
     * @return bool
     */
    public static function isLumen()
    {
        return function_exists('app')
            && preg_match('/lumen/i', app()->version());
    }
}