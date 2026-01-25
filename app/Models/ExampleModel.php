<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * Example model showing how to use Spatie Translatable and Sluggable traits
 * 
 * This is a reference model - you can delete it and apply these traits to your actual models
 */
class ExampleModel extends Model
{
    use HasFactory, HasTranslations, HasSlug;

    /**
     * The attributes that are translatable
     *
     * @var array<int, string>
     */
    public $translatable = [
        'title',
        'description',
        'content',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateUniqueSlugs(); // Remove this if you want unique slugs
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'content',
        'slug',
    ];
}
