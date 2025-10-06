<?php

return [
    'accepted'             => ':attribute を承認してください。',
    'active_url'           => ':attribute は有効なURLではありません。',
    'after'                => ':attribute には :date 以降の日付を指定してください。',
    'alpha'                => ':attribute にはアルファベットのみ使用できます。',
    'alpha_dash'           => ':attribute にはアルファベット、数字、ダッシュ（-）、下線（_）が使用できます。',
    'alpha_num'            => ':attribute にはアルファベットと数字が使用できます。',
    'array'                => ':attribute には配列を指定してください。',
    'before'               => ':attribute には :date 以前の日付を指定してください。',
    'between'              => [
        'numeric' => ':attribute は :min から :max の間で指定してください。',
        'file'    => ':attribute は :min KBから :max KBの間で指定してください。',
        'string'  => ':attribute は :min 文字から :max 文字の間で指定してください。',
        'array'   => ':attribute は :min 個から :max 個の間で指定してください。',
    ],
    'confirmed'            => ':attribute が確認用と一致していません。',
    'email'                => ':attribute には有効なメールアドレスを指定してください。',
    'unique'               => 'この:attribute はすでに使用されています。',
    'required'             => ':attribute は必須です。',
    'min'                  => [
        'string'  => ':attribute は :min 文字以上で入力してください。',
    ],
    'max'                  => [
        'string'  => ':attribute は :max 文字以内で入力してください。',
    ],

    'attributes' => [
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'name' => '名前',
    ],
];
