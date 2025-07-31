<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language');

        // 可以根據需求定義允許的語系列表
        $supportedLocales = ['en', 'zh_TW', 'zh_CN'];

        if ($locale && in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        } else {
            // 如果沒有 Accept-Language 或不支持，使用 config/app.php 中的預設語系
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
