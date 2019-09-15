<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Feed;
use App\BackupPost;
use App\LiveBroadcast;
use App\Events\LiveBroadcasting;
use Auth;
use App\User;
use \Carbon\Carbon;


class PostController extends Controller
{

	public function show($feed,$id,$uri)
	{
		$feed = Feed::whereUri($feed)->first();
		$post = Post::whereId($id)->whereUri($uri)->whereFeed($feed->id)->first();

		$backups='';
    $user = Auth::user();
		if ($user && $user->isOne(['admin']))
		  $backups= BackupPost::whereId_posts($id)->get(['*']);

		if (!$post) return abort(404);

		$tpl = 'posts.show';
		if ($feed->id == 3){
			$tpl = 'posts.review';
		}
    //Получение для трансляций лайв-сообщений из БД
    $live_messages= '';
    if ($feed->id == 5){
      $live_messages= LiveBroadcast::whereId_posts($id)->orderBy('created_at','desc')->get(['*']);
      foreach ($live_messages as $message){
        $message-> user= User::getAuthor($message-> user_id);
      }
    }

		$post->data = preg_replace("/img src=\"\/images\/uploads\/thumb\/(.*?)\.jpg\"/", "img src=\"/imager/570/$1.jpg\"", $post->data);

		$post->data = preg_replace('/<p><br><\/p>/', '', $post->data);

		$post->addPageViewThatExpiresAt(Carbon::now()->addSeconds(10));

		return view($tpl , ['post'=>$post, 'backups'=>$backups, 'live_messages'=>$live_messages, 'options'=>false]);
	}

	protected function backup($id_posts){
	  $post = Post::whereId($id_posts);
    $post = $post->first();
    $arr=[];
	  $backup= new BackupPost;

    foreach ($backup->getFillable() as $key){
      $arr[$key]  = $post->$key;
    }
    $arr['id_posts']= $id_posts;
    $backup = new BackupPost($arr);

    $backup->save();

    if (!$backup) return abort(404);
    return redirect(route('post.show',['feed'=>$post->feed()->uri,'id'=>$post->id,'uri'=>$post->uri]));
  }

  protected function recovery($id, $id_posts){
    $old_version= BackupPost::whereId($id)->get(['*']);
    $backup=[];
		foreach ($old_version as $item){
      $backup['id']= $item['id'];
      $backup['date']= $item['created_at'];
      $backup['h1']= $item['h1'];
      $backup['data']= $item['data'];
    }
    Post::whereId($id_posts)->update(['created_at'=> $backup['date'], 'h1'=> $backup['h1'], 'data'=> $backup['data']]);
    $post= Post::whereId($id_posts)->first();

    return redirect(route('post.show',['feed'=>$post->feed()->uri,'id'=>$post['id'],'uri'=>$post['uri']]));
  }

  protected function live_add (Request $request, $post_id){

    $post = Post::where('id', $post_id);

    $user = Auth::user();
    if (!$user->isOne(['admin', 'editor'])){
      $post = $post->where('user_id',$user->id);
    }

    $post = $post->first();
    if ($post_id && !$post) return abort(404);

    return view('posts.live_add',['formData'=>['id'=> $post_id], 'post'=> $post]);
  }

  protected function live_save (Request $request){
    $id = Auth::id();
    if (!$id) return abort(404);

    $live_br= new LiveBroadcast;
    $live_br-> user_id= $id;

    $live_br-> id_posts= $request->request->get('id');
    $live_br-> data= $request->request->get('data');

    $live_br-> save();

    $id_posts = $request->request->get('id');
    $user_id = $id;
    $data = $request->request->get('data');
    event (new LiveBroadcasting ($id_posts, $user_id, $data));

    return 1;
  }

  protected function live_show ($post_id){
    $live_messages= LiveBroadcast::whereId_posts($post_id)->orderBy('created_at','desc')->get(['*']);

    foreach ($live_messages as $message){
      $message-> user= User::getAuthor($message-> user_id);
    }

    return view('posts.live_show', ['live_messages' => $live_messages, 'options'=> 1]);
  }

  protected function live_edit (Request $request, $msg_id){
    $live_msg = LiveBroadcast::whereId($msg_id)->first();
    $live_msg->data = $request->request->get('data');
    $live_msg->save();

    return 1;
  }

  protected function live_delete ($msg_id){
    LiveBroadcast::whereId($msg_id)->delete();

    return 1;

  }
}