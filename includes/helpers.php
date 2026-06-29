<?php

class helper {
    private static function get($type, string $key, int $filter = FILTER_DEFAULT, $options = null) {
        return filter_input($type, $key, $filter, $options);
    }
    
    public static function get_str(string $key, $filter = FILTER_SANITIZE_SPECIAL_CHARS): ?string {
        return self::get(INPUT_GET, $key, $filter);
    }
    
    public static function get_int(string $key): ?int {
        return self::get(INPUT_GET, $key, FILTER_VALIDATE_INT);
    }
    
    public static function post_str(string $key, mixed $filter = FILTER_SANITIZE_SPECIAL_CHARS): ?string {
        $value = self::get(INPUT_POST, $key, $filter);
        
        if (trim($value) === '' || $value === null) return false;
        
        return $value;
    }
    
    
    
    
    public static function fetch_post($key, $filter = null) {
        
        if ($filter !== null) {
            return filter_input(INPUT_POST, $key, $filter) ?: false;
        }

        $value = $_POST[$key] ?? false;

        if ($value === false) {
            return false;
        }

        $value = trim($value);

        return $value !== '' ? $value : false;
    }
    
    public static function fetch_global(array $array, string $key) {
        $value = $array["$key"] ?? null;
        
        if ($value !== null) return $value;
        return false;
    }
    
    public static function log(mixed $content, string $filename = "stuff.txt") {
	file_put_contents($filename, $content, FILE_APPEND);
    }
}

abstract class time {
    public const SECOND = 1;
    public const MINUTE = 60 * self::SECOND;
    public const HOUR = 60 * self::MINUTE;
    public const DAY = 24 * self::HOUR;
    public const WEEK = 7 * self::DAY;
    public const MONTH = 30 * self::DAY;
    
     public static function timeAgo(int $seconds): string {
        $units = [
            'year' => 31536000,
            'month' => 2592000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1,
        ];

        foreach ($units as $name => $value) {
            if ($seconds >= $value) {
                $count = floor($seconds / $value);

                return $count . ' ' . $name . ($count != 1 ? 's' : '');
            }
        }

        return 'just now';
    }
}

class img_helper {
    
    public static function process_img($image): array {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($info, $image['tmp_name']);
        
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];
        
        $set = isset($allowed[$mime]);
        
        if ($set === false) {
            return [
                'valid' => false,
                'type' => null
            ];
        }
        
        $ext = $allowed[$mime];
        
        return [
            'valid' => true,
            'type' => $ext,
            'new_name' => bin2hex(random_bytes(16)) . '.' . $ext
        ];
    }
    
    
    
}

class response {

	public static function redirect(string $path): never {
	    header("Location: $path");
	    exit;
	}
	
	public static function echo_json(array $array): void {
	    header('Content-Type: application/json');
	    
	    echo json_encode($array);
	    exit;
	}
    }

