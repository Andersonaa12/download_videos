<?php

namespace App\Models\Download;

use Illuminate\Database\Eloquent\Model;

class DownloadStatus extends Model
{
    protected $table = 'download_status';
    public const ID_PENDIENTE = 1;
    public const ID_PROCESANDO = 2;
    public const ID_COMPLETADO = 3;
    public const ID_FALLIDO = 4;

    protected $fillable = [
        'name',
        'description',
    ];
}