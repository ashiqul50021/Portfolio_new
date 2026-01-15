<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    use HasFactory;

    protected $fillable = ['portfolio_id', 'image', 'is_primary', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
