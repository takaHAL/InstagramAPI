<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\InstagramToken;

class Instagram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instagram access token update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $baseUrl = "https://graph.instagram.com/refresh_access_token?";

      // アクセストークン取得
      $accessToken = InstagramToken::select('access_token')->latest()->limit(1)->get();

      // パラメーター設定
      $params = array(
        'grant_type' => 'ig_refresh_token',
        'access_token' => !$accessToken ? $accessToken : env('INSTAGRAM_TOKEN')
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $baseUrl . http_build_query($params));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $responseText = curl_exec($ch);
      $result = json_decode($responseText, true);

      curl_close($ch);

      // DB保存
      if ($newAccessToken = $result['access_token']) {
        $instagramToken = new InstagramToken();
        $instagramToken->access_token = $newAccessToken;
        $instagramToken->token_type = $result['token_type'];
        $instagramToken->expires_in = $result['expires_in'];
        $instagramToken->save();
      }

      logger()->info($params);
    }
}
