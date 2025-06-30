<?php

namespace App\Models;

use App\Traits\HasContactNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperWhatsappMessage
 */
class WhatsappMessage extends BaseModel
{
    use HasContactNumber;
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($q) {
            if (! $q->ip_address) {
                $q->ip_address = request()->getClientIp();
            }
        });
    }

    public function getCasts(): array
    {
        return [
            'admin_id' => 'string',
            'session_id' => 'string',
            'contact_country_id' => 'integer',
            'contact_number' => 'string',
            'full_contact_number' => 'string',
            'message' => 'string',
            'ip_address' => 'string',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = trim(strip_tags($value));
    }
}
