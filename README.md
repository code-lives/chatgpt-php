# 右上角收藏

### 安装说明

    composer require code-lives/chat-gpt

### Laravel 操作方式 使用 Redis 存储聊天记录

```php

    $uid = "1"; //要求是字符串
    $redis_key = "chat_record" . $uid;
    $content = "还有哪些优点"; //提问内容
    $chat = Redis::get($redis_key);
    if (!$chat) {
        $chatArray = [['role' => 'user', 'content' => $content]];
    } else {
        $chatArray = unserialize($chat);
        $chatArray[] = ['role' => 'user', 'content' => $content];
    }
    $chat = new \ChatGpt\Chat\ChatGpt('key值');
    $data = $chat->QuestionAndAnswer($uid, $chatArray);
    if (isset($data['choices'])) {
        $chatArray[] = ['role' => 'assistant', 'content' => $data['choices'][0]['message']['content']];
    }
    Redis::setex($redis_key, 3600, serialize($chatArray));
    echo $data['choices'][0]['message']['content'];

    在 Laravel 视图层展示可能标签丢失 {{$data}} 转换 {!!$data!!}

```

### 代码块如何实现在 html 和 README 文件类似效果

```php

    $chat = new \ChatGpt\Chat\ChatGpt('key值');
    $data = $chat->QuestionAndAnswer($uid, $chatArray);
    $content = $chat->WebFormat($data['choices'][0]['message']['content']);

```

### html 代码 进行处理代码块,加上以下这些就行

```php
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.2.0/styles/default.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.2.0/highlight.min.js"></script>
<script>
  hljs.initHighlightingOnLoad();
</script>
```

### 聊天记录存入 mysql 格式如下

> 问答数据存取顺序注意。用户提问 存入，chatgpt 返回答案存入。看你怎么操作了。

```php

    id 自增
    uid 用户uid
    role 角色 用户=user  chatgpt = assistant
    content 内容
    create_time int

    读取出来的数据如下
    $chatArray=[
        [
            'role'=>'user',
            'content'=>'Laravel 有什么优点',
        ],
        [
            'role'=>'assistant',
            'content'=>'有****优点',
        ],
        [
            'role'=>'user',
            'content'=>'还有吗？'
        ],
        [
            'role'=>'assistant',
            'content'=>'还有****',
        ]
    ];

```

### chatArray 格式说明

第一次用户提问

```php

$chatArray=[
    [
    'role'=>'user',
    'content'=>'Laravel 有什么优点',
    ]
];

```

第二次用户提问,包含了 第一次 chatgpt 回复的内容.如果没有可能上下文丢失，匹配不到最佳内容

```php

    $chatArray=[
        [
        'role'=>'user',
        'content'=>'Laravel 有什么优点',
        ],
        ,
        [
        'role'=>'assistant',
        'content'=>'有****优点',
        ],
        [
        'role'=>'user',
        'content'=>'还有吗？'
        ]
    ];

```
