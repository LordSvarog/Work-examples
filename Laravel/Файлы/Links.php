<?php
namespace App;

use Image;

class Links {

  public static function correctLinks($text) {
    return preg_replace_callback('~(http|https|ftp|ftps)://[^\s]+~siu',
    'self::shortLink', $text);
  }

  private static function shortLink($matches) {
    $left = 9; //часть текста слева
    $right = 16; //часть текста справа
    $in = ' ... '; //вставка
    $enc = 'UTF-8';
    $linkText = $matches[0];
    $len = mb_strlen($linkText, $enc);

    if ($len > ($left + $right + mb_strlen($in, $enc))) {
      $linkText = mb_substr($linkText, 0, $left, $enc) .
      $in .
      mb_substr($linkText, $len - $right, $right, $enc);
    }
    return '<a href="' . $matches[0] . '">' . $linkText . '</a>';
  }
}