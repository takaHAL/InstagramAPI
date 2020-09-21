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
    $accessToken = InstagramToken::select('access_token')->latest()->limit(1)->get();

    // パラメーター設定
    $params = array(
      'fields' => implode(',', array('id','caption','permalink','media_url','thumbnail_url')),
      'access_token' => !$accessToken ? $accessToken : env('INSTAGRAM_TOKEN')
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
