<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{ 
    public function handle(Request $request)
    {
        $input = $request->all();
        $message = $input['message'];
        $chat_id = $message['chat']['id'];
        $text = $message['text'];

        if($text == '/start')
        {
            $this->call('sendMessage',[
                'chat_id' => $chat_id,
                'text' => "Assalomu Alaykum"
            ]);
        }else{
            $translationFunction = preg_match('/\p{Cyrillic}+/u', $text) ? 'translateKrLo' : 'translateLoKr';

            $this->call('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $this->$translationFunction($text),
            ]);

        }
    }
    private function call( string $method, $params = [])
    {
        $url = "https://api.telegram.org/bot" . config('services.telegram.api_key') . "/" . $method;
        $response = Http::post($url,$params);
        return $response->json();
    }
    private function translateLoKr($text)
    {
        $translit = str_replace(
        [
        'Ya','ya','Yu','yu','Yo\'','yo\'','Yo‘','yo‘','Ch','ch','O\'','o\'','O`','o`','O‘','o‘','G\'','g\'','G‘','g‘'
        ,'Sh','sh','A','a','B','b','D','d','E','e','F','f','G','g','H','h','I','i','J','j','K','k','L','l','M','m','N'
        ,'n','O','o','P','p','Q','q','R','r','S','s','T','t','U','u','V','v','X','x','Y','y','Z','z','Ng','ng','\'','’'
        ],

        [
        'Я','я','Ю','ю','Ё','ё','Ё','ё','Ч','ч','Ў','ў','Ў','ў','Ў','ў','Ғ','ғ','Ғ','ғ','Ш','ш','А','а',
        'Б','б','Д','д','Э','э','Ф','ф','Г','г','Ҳ','ҳ','И','и','Ж','ж','К','к','Л','л','М','м','Н','н',
        'О','о','П','п','Қ','қ','Р','р','С','с','Т','т','У','у','В','в','Х','х','Й','й','З','з','Нг','нг','ъ','ъ'
        ],
            $text
        );
        
        return $translit;
    }
    
    private function translateKrLo($text)
    {
        $translit = str_replace(
            [
            'А','а','Б','б','Д', 'д','Э','э','Ф','ф','Г','г','Ғ','ғ','Ҳ','ҳ','И','и','Ж','ж','К','к','Л','л','М','м','Н','н','О','о','Ў',
            'ў','П','п','Қ','қ','Р','р','С','с','Ш','ш','Ч','ч','Т','т','У','у','В','в','Х','х','Й','й','З','з','Нг','нг','ъ','Я','я','Ю','ю','Ё','ё'
            ],

            ['A','a','B','b','D','d','E','e','F','f','G','g','G\'','g\'','H','h','I','i','J','j','K','k','L','l','M','m','N','n','O','o','O\'',
            'o\'','P','p','Q','q','R','r','S','s','Sh','sh','Ch','ch','T','t','U','u','V','v','X','x','Y','y','Z','z','Ng','ng','\'','Ya','ya','Yu','yu','Yo','yo'
            ],

            $text
        );
        
        return $translit;
    }
}
