<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    // Nama table sebenar di MySQL
    protected $table = 'asnp___ninja_van_at_pengkalan_chepa___dec_24';

    // Jika table tiada timestamp columns created_at/updated_at
    public $timestamps = false;

    // Biarkan guarded kosong supaya boleh mass assign (jika perlu)
    protected $guarded = [];
}
