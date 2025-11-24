<?php

namespace App\Core;

class Bencode
{
    public static function encode($data)
    {
        if (is_int($data)) {
            return 'i' . $data . 'e';
        } elseif (is_string($data)) {
            return strlen($data) . ':' . $data;
        } elseif (is_array($data)) {
            if (self::isList($data)) {
                $result = 'l';
                foreach ($data as $item) {
                    $result .= self::encode($item);
                }
                return $result . 'e';
            } else {
                ksort($data);
                $result = 'd';
                foreach ($data as $key => $value) {
                    $result .= self::encode($key) . self::encode($value);
                }
                return $result . 'e';
            }
        }
        return '';
    }

    public static function decode($data)
    {
        $position = 0;
        return self::decodeValue($data, $position);
    }

    private static function decodeValue($data, &$position)
    {
        if ($position >= strlen($data)) {
            return null;
        }

        $char = $data[$position];
        
        if ($char === 'i') {
            $position++;
            $end = strpos($data, 'e', $position);
            if ($end === false) {
                throw new \RuntimeException('Invalid bencode: integer not terminated');
            }
            $value = (int) substr($data, $position, $end - $position);
            $position = $end + 1;
            return $value;
        } elseif ($char === 'l') {
            $position++;
            $list = [];
            while ($position < strlen($data) && $data[$position] !== 'e') {
                $list[] = self::decodeValue($data, $position);
            }
            if ($position >= strlen($data)) {
                throw new \RuntimeException('Invalid bencode: list not terminated');
            }
            $position++;
            return $list;
        } elseif ($char === 'd') {
            $position++;
            $dict = [];
            while ($position < strlen($data) && $data[$position] !== 'e') {
                $key = self::decodeValue($data, $position);
                $value = self::decodeValue($data, $position);
                $dict[$key] = $value;
            }
            if ($position >= strlen($data)) {
                throw new \RuntimeException('Invalid bencode: dictionary not terminated');
            }
            $position++;
            return $dict;
        } elseif (ctype_digit($char)) {
            $colon = strpos($data, ':', $position);
            if ($colon === false) {
                throw new \RuntimeException('Invalid bencode: string length missing colon');
            }
            $length = (int) substr($data, $position, $colon - $position);
            $position = $colon + 1;
            if ($position + $length > strlen($data)) {
                throw new \RuntimeException('Invalid bencode: string length exceeds data');
            }
            $value = substr($data, $position, $length);
            $position += $length;
            return $value;
        }
        
        return null;
    }

    private static function isList(array $array): bool
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}


