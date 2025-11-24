<?php

namespace App\Core;

class FormatHelper
{
    public static function bytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes < 1099511627776) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } else {
            return number_format($bytes / 1099511627776, 2) . ' TB';
        }
    }

    public static function date(int $timestamp, string $format = 'M j, Y'): string
    {
        return date($format, $timestamp);
    }

    public static function timeAgo(int $timestamp): string
    {
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return self::date($timestamp);
        }
    }

    public static function ratio(float $uploaded, float $downloaded): string
    {
        if ($downloaded == 0) {
            return $uploaded > 0 ? '∞' : '0.00';
        }
        return number_format($uploaded / $downloaded, 2);
    }

    public static function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function bbcode(string $text): string
    {
        // Basic BBCode parsing
        $text = htmlspecialchars($text);
        
        // Bold
        $text = preg_replace('/\[b\](.*?)\[\/b\]/is', '<strong>$1</strong>', $text);
        
        // Italic
        $text = preg_replace('/\[i\](.*?)\[\/i\]/is', '<em>$1</em>', $text);
        
        // Underline
        $text = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $text);
        
        // Color
        $text = preg_replace('/\[color=([^\]]+)\](.*?)\[\/color\]/is', '<span style="color:$1">$2</span>', $text);
        
        // Size
        $text = preg_replace('/\[size=(\d+)\](.*?)\[\/size\]/is', '<span style="font-size:$1px">$2</span>', $text);
        
        // Font
        $text = preg_replace('/\[font=([^\]]+)\](.*?)\[\/font\]/is', '<span style="font-family:$1">$2</span>', $text);
        
        // URLs
        $text = preg_replace('/\[url\](.*?)\[\/url\]/is', '<a href="$1" target="_blank">$1</a>', $text);
        $text = preg_replace('/\[url=([^\]]+)\](.*?)\[\/url\]/is', '<a href="$1" target="_blank">$2</a>', $text);
        
        // Images
        $text = preg_replace('/\[img=([^\]]+)\]/is', '<img src="$1" alt="" />', $text);
        $text = preg_replace('/\[img\](.*?)\[\/img\]/is', '<img src="$1" alt="" />', $text);
        
        // Quotes
        $text = preg_replace('/\[quote\](.*?)\[\/quote\]/is', '<blockquote>$1</blockquote>', $text);
        $text = preg_replace('/\[quote=([^\]]+)\](.*?)\[\/quote\]/is', '<blockquote><strong>$1 wrote:</strong><br>$2</blockquote>', $text);
        
        // Lists
        $text = preg_replace('/\[\*\](.*?)(?=\[\*\]|$)/is', '<li>$1</li>', $text);
        $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
        
        // Preformat
        $text = preg_replace('/\[pre\](.*?)\[\/pre\]/is', '<pre>$1</pre>', $text);
        
        // Auto-link URLs
        $text = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank">$1</a>', $text);
        
        return nl2br($text);
    }
}


