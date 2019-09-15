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


class BackupPost extends Model
{
  use Taggable,Favoriteable,HasViewCounter;

  protected $primaryKey = 'id';
  protected $user;

  protected $table= 'backup_posts';
  //public $timestamps= false;

  protected $fillable = [
    'id_posts',
    'post_js_block',
    'h1', 'h1_en',
    'keywords',
    'description',
    'via',
    'source',
    'ann',
    'data', 'data_en',
    'user_id',
    'feed',
    'video',
  ];

  protected $dates = [
    'created_at',
    'updated_at',
  ];

  public function setH1Attribute($value)
  {
    $value = trim($value);
    $this->attributes['h1'] = $value;
  }

  function mime_content_type_new($filename) {

    $mime_types = array(

      'txt' => 'text/plain',
      'htm' => 'text/html',
      'html' => 'text/html',
      'php' => 'text/html',
      'css' => 'text/css',
      'js' => 'application/javascript',
      'json' => 'application/json',
      'xml' => 'application/xml',
      'swf' => 'application/x-shockwave-flash',
      'flv' => 'video/x-flv',

      // images
      'png' => 'image/png',
      'jpe' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg' => 'image/jpeg',
      'gif' => 'image/gif',
      'bmp' => 'image/bmp',
      'ico' => 'image/vnd.microsoft.icon',
      'tiff' => 'image/tiff',
      'tif' => 'image/tiff',
      'svg' => 'image/svg+xml',
      'svgz' => 'image/svg+xml',

      // archives
      'zip' => 'application/zip',
      'rar' => 'application/x-rar-compressed',
      'exe' => 'application/x-msdownload',
      'msi' => 'application/x-msdownload',
      'cab' => 'application/vnd.ms-cab-compressed',

      // audio/video
      'mp3' => 'audio/mpeg',
      'qt' => 'video/quicktime',
      'mov' => 'video/quicktime',

      // adobe
      'pdf' => 'application/pdf',
      'psd' => 'image/vnd.adobe.photoshop',
      'ai' => 'application/postscript',
      'eps' => 'application/postscript',
      'ps' => 'application/postscript',

      // ms office
      'doc' => 'application/msword',
      'rtf' => 'application/rtf',
      'xls' => 'application/vnd.ms-excel',
      'ppt' => 'application/vnd.ms-powerpoint',

      // open office
      'odt' => 'application/vnd.oasis.opendocument.text',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $arr=explode('.',$filename);
    $ext = strtolower(array_pop($arr));
    if (array_key_exists($ext, $mime_types)) {
      return $mime_types[$ext];
    }else{
      return 'application/octet-stream';
    }
  }

  public function enclosures(){

    $return=[];

    preg_match_all("/<img.*?data\-full=\"(.*?)\"/", $this->attributes['data'], $mt);
    //print_r($mt);
    foreach ($mt[1] as $rw){
      $return[]=[$rw, $this->mime_content_type_new($rw) ];
    }
    return $return;
  }

  public function user(){
    return $this->belongsTo(User::class);
  }

  public function feed(){
    return $this->hasOne('App\Feed','id','feed')->getResults();
  }
}