<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
    protected $fillable = ['title','project_url','image','cat_id'];

    public function category(){
        return $this->belongsTo(Category::class,'cat_id');
    }

    public function images(){
        return $this->hasMany(PortfolioImage::class)->orderBy('sort_order');
    }

    public function primaryImage(){
        return $this->hasOne(PortfolioImage::class)->where('is_primary', true);
    }

    // Get the display image (primary image, first uploaded image, or legacy image field)
    public function getDisplayImageAttribute(){
        if($this->primaryImage){
            return $this->primaryImage->image;
        }
        if($this->images->count() > 0){
            return $this->images->first()->image;
        }
        return $this->image; // fallback to legacy single image
    }
}
