<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileDownload extends Model
{
    protected $table = 'file_download';
    protected $fillable = ['nama', 'deskripsi', 'file_path', 'nama_file_asli', 'kategori', 'jumlah_download', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function incrementDownload(): void
    {
        $this->increment('jumlah_download');
    }
}
