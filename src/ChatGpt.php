<?php

namespace ChatGpt\Chat;

class ChatGpt
{
    private $key;
    private $QuestionAndAnswerUrl = "https://api.openai.com/v1/chat/completions";
    public function __construct($key)
    {
        if (!$key) {
            throw new \Exception("Not Key");
        }
        $this->key = $key;
    }
    public function QuestionAndAnswer(string $uid, array $content)
    {
        $parm = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $content,
            'temperature' => 0.7,
            'user' => $uid,
        ];
        return $this->curl_post($this->QuestionAndAnswerUrl, $parm);
    }
    public function curl_post($url, $data)
    {
        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->key
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            throw new \Exception("Curl Error" . $error_msg);
        }
        curl_close($curl);
        return json_decode($response, true);
    }
    public function WebFormat($string)
    {
        $content = preg_replace_callback('/```(\w+)?(.*?)```/s', function ($matches) {
            $language = $matches[1] ?? 'default';
            $code = $matches[2];
            // 对代码块中的内容不进行 HTML 转义和换行符转换
            $code = str_replace(["\r\n", "\r", "\n"], "\n", $code);
            return "<pre><code class='hljs $language'>$code</code></pre>";
        }, $string);
        return nl2br($content);
    }
}
