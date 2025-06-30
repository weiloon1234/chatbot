<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAuditTrail
 */
class AuditTrail extends Model
{
    use HasFactory;

    public function getCasts(): array
    {
        return [
            'admin_id' => 'integer',
            'action' => 'string',
            'created_data' => 'array',
            'old_data' => 'array',
            'edited_data' => 'array',
        ];
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

    public function setOperationAttribute($value)
    {
        $this->attributes['operation'] = mb_strtoupper($value);
    }

    public function setCreatedDataAttribute($value)
    {
        $this->attributes['created_data'] = json_encode($value);
    }

    public function setOldDataAttribute($value)
    {
        $this->attributes['old_data'] = json_encode($value);
    }

    public function setEditedDataAttribute($value)
    {
        $this->attributes['edited_data'] = json_encode($value);
    }
}
