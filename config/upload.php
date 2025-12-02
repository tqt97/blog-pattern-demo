<?php

return [

    'types' => [

        'posts' => [
            'disk' => 'public',
            'folder' => 'uploads/posts',

            'rules' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // KB
                'dimensions:min_width=200,min_height=200',
            ],
        ],

        'banners' => [
            'disk' => 'public',
            'folder' => 'uploads/banners',

            'rules' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=1200,min_height=300',
            ],
        ],

        'default' => [
            'disk' => 'public',
            'folder' => 'uploads/others',

            'rules' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ],
    ],
];
