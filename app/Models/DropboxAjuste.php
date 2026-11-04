<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DropboxAjuste extends Model
{
    protected $table = 'dropbox_ajustes';

    protected $primaryKey = 'item_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['oculto' => 'boolean'];
    }
}
