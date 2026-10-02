<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Karyawan extends Model
{
    protected $table = 'karyawans';
    protected $primaryKey = 'fid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['fid', 'nama_karyawan', 'divisi', 'jabatan', 'status'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'fid', 'fid');
    }
}
