<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InstagramToken;

class InstagramController extends Controller
{
  public function index ()
  {
    $baseUrl = "https://graph.instagram.com/me/media?";

    // アクセストークン取得
    $instagramToken = InstagramToken::select('access_token')->latest()->first();
    $accessToken = env('INSTAGRAM_TOKEN');

    if ($instagramToken->access_token) {
      $accessToken = $instagramToken->access_token;
    }

    // パラメーター設定
    $params = array(
      'fields' => implode(',', array('id','caption','permalink','media_url','thumbnail_url')),
      'access_token' => $accessToken
    );

    //curlセッション初期化
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $baseUrl . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $responseText = curl_exec($ch);
    $result = json_decode($responseText, true);

    //セッション終了
    curl_close($ch);

    return view('instagram', [
      'mediaData' => $result['data'],
      'paging' => $result['paging']
    ]);
  }
}
