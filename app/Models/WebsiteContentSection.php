<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteContentSection extends Model
{
    use  HasFactory;
    protected $table = 'website_content_sections';
    public $timestamps = 0;
     public function getFillable(){
        return  $this->getTableColumns();
     }
     public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
  
    public function website_banner(): BelongsTo
    {

        return $this->belongsTo(WebsiteBanner::class, 'website_banner_id', 'id', );

    }
    public function website_slider(): BelongsTo
    {

        return $this->belongsTo(WebsiteSlider::class, 'website_slider_id', 'id', );

    }

   
}