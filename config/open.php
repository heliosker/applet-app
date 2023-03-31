<?php

return [

    'wechat_app_id' => env('WECHAT_APP_ID', ''),


    'wechat_app_secret' => env('WECHAT_APP_SECRET', ''),


    'openai_api_key' => env('OPENAI_API_KEY', ''),


    'openai_model' => env('OPENAI_MODEL', ''),


    /**
     * 代理 URL
     */
    'openai_base_url' => env('OPENAI_BASE_URL', ''),


    /**
     * ON-OFF
     */
    'ai_qa' => env('AI_QA', 0),

    'baby_name' => env('BABY_NAME', 0),

    'ai_draw' => env('AI_DRAW', 0),


];
