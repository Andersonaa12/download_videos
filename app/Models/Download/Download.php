<?php

namespace App\Models\Download;

use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;
use App\Models\Download\DownloadStatus;
class Download extends Model
{
    protected $fillable = [
        'name',
        'url',
        'status_id',
        'file_path',
        'file_name',
        'error_message',
        'created_by',
        'updated_by',
    ];

    public function Status()
    {
        return $this->belongsTo(DownloadStatus::class, 'status_id');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function UpdatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}