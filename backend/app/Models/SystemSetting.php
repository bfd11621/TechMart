<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get typed value with decryption / json decoding.
     */
    public function getParsedValue(bool $maskSensitive = true): mixed
    {
        if (is_null($this->value)) {
            return null;
        }

        switch ($this->type) {
            case 'encrypted':
                if ($maskSensitive) {
                    return '••••••••••••';
                }
                try {
                    return Crypt::decryptString($this->value);
                } catch (Exception) {
                    return $this->value;
                }
            case 'json':
                $decoded = json_decode($this->value, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $this->value;
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            default:
                return $this->value;
        }
    }

    /**
     * Set typed value with encryption / json encoding.
     */
    public function setParsedValue(mixed $val): void
    {
        if (is_null($val)) {
            $this->value = null;
            return;
        }

        switch ($this->type) {
            case 'encrypted':
                // If masked value passed back, keep existing value
                if ($val === '••••••••••••' || $val === '••••••••') {
                    return;
                }
                $this->value = Crypt::encryptString($val);
                break;
            case 'json':
                $this->value = is_string($val) ? $val : json_encode($val);
                break;
            case 'boolean':
                $this->value = $val ? '1' : '0';
                break;
            default:
                $this->value = (string) $val;
                break;
        }
    }

    /**
     * Helper static getter
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        return $setting->getParsedValue(false);
    }

    /**
     * Helper static setter
     */
    public static function set(string $key, mixed $value, string $group = 'system', string $type = 'string', bool $isPublic = false): static
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->group = $group;
        $setting->type = $type;
        $setting->is_public = $isPublic;
        $setting->setParsedValue($value);
        $setting->save();

        return $setting;
    }
}
