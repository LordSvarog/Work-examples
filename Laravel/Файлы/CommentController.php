<?php

namespace App\Http\Controllers;

use risul\LaravelLikeComment\Controllers\CommentController as CC;
use risul\LaravelLikeComment\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends CC
{

  public function getCommentsForYa(Request $request){
    $GLOBALS['commentVisit'] = array();
    $offset= $request-> offset;
    $limit= $request-> limit;
    $item_id= $this->getItemId($request-> ORIGINAL_URL);

    $comments = self::getComments($item_id);

    $total= [];
    foreach ($comments as $comment) {
      if(!isset($GLOBALS['commentVisit'][$comment->id])) {
        $total[] = $this-> buildList($comments, $comment);
      }
    }
    return response()-> json(['offset'=> $offset, 'limit'=> $limit, 'total'=> $limit, 'comments'=> $total],200);
  }

  public function saveCommentFromYa (Request $request){
    $userId = Auth::id();
    isset($request->answer_to) ? $parent= $request->answer_to : $parent= 0;
    $item_id= $this->getItemId($request-> ORIGINAL_URL);
    $commentBody = $request->text;

    $comment= new Comment;
    $comment-> user_id= $userId;
    $comment-> parent_id= $parent;
    $comment-> item_id= $item_id;
    $comment-> comment= $commentBody;

    $comment-> save();

    $id= $comment-> id;
    $date= $comment-> created_at-> getTimestamp();

    return response()-> json(['id'=> $id, 'date'=> $date],200);
  }

  protected function buildList($comments, $comment){
    $GLOBALS['commentVisit'][$comment->id] = 1;
    $item= [
      'name'=> $comment->name,
      'date'=> $comment->updated_at->getTimestamp(),
      'content'=> preg_replace('/(\\R{2})\\R++/', '$1', $comment->comment),
      'id'=> $comment->id,
    ];
    $replies=[];
    foreach ($comments as $child) {
      if($child->parent_id == $comment->id && !isset($GLOBALS['commentVisit'][$child->id])){
        $replie= $this-> buildList($comments, $child);
        $replie['answer_to']= $child->parent_id;
        $replies[]= $replie;
      }
    }
    $item['replies']= $replies;
    return $item;
  }

  protected function getItemId($URL){
    $URL= explode("/", $URL);
    $items= explode("-", $URL[4]);
    $item_id= 'post_' . $items[0];
    return $item_id;
  }
}