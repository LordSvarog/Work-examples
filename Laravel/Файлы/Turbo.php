<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use risul\LaravelLikeComment\Models\Comment;
use App\User;
use Cviebrock\EloquentTaggable\Models\Tag;
use risul\LaravelLikeComment\Models\TotalLike;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use App\Traits\HasViewCounter;


class Turbo extends Model
{
  use Taggable,Favoriteable,HasViewCounter;

  protected $primaryKey = 'id';
  protected $user;

  protected $table= 'turbo';
  //public $timestamps= false;

  protected $fillable = [
    'turbo_id',
    'user_id',
  ];

  protected $dates = [
    'created_at',
    'updated_at',
  ];

  public function user(){
    return $this->belongsTo(User::class);
  }
}