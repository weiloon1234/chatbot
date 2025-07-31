<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperBotMessage
 */
class BotMessage extends BaseModel
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'channel', 'direction',
        'from_id', 'from_name', 'to_id', 'to_name',
        'message', 'type', 'status',
        'sent_at', 'received_at',
        'external_message_id', 'response',
        'reference_number', 'ip_address',
    ];

    protected $casts = [
        'id' => 'string',
        'channel' => 'string',
        'direction' => 'string',
        'from_id' => 'string',
        'from_name' => 'string',
        'to_id' => 'string',
        'to_name' => 'string',
        'message' => 'array',
        'raw_payload' => 'array',
        'type' => 'string',
        'status' => 'string',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'external_message_id' => 'string',
        'response' => 'array',
        'reference_number' => 'string',
        'ip_address' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Str::uuid();
            }
            if (empty($model->ip_address)) {
                $model->ip_address = request()->ip();
            }
        });
    }

    public static function getStatusList()
    {
        return [
            'PENDING' => __('Pending'),
            'SENT' => __('Sent'),
            'FAILED' => __('Failed'),
            'RECEIVED' => __('Received'),
            'READ' => __('Read'),
        ];
    }
}
