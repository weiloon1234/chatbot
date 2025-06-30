<?php

namespace App\Models;

use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperDepositReceipt
 */
class DepositReceipt extends BaseModel
{
    use HasFactory;
    use UploadFile;

    protected $casts = [
        'deposit_id' => 'integer',
        'file_path' => 'string',
    ];

    public function fileColumns()
    {
        return [
            'file_path' => [
                'path' => '/deposit/receipt/',
            ],
        ];
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id', 'id');
    }
}
