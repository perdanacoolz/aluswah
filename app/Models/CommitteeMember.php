<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    protected $fillable = [
        'name',
        'position',
        'division',
        'photo_path',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path 
            ? asset('storage/' . $this->photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=200&background=059669&color=fff';
    }
}
