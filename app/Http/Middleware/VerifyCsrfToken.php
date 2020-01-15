<?php
/*
 * @Author: your name
 * @Date: 2019-10-28 09:17:20
 * @LastEditTime: 2019-12-25 19:30:21
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: \htdocs\Laravel\app\Http\Middleware\VerifyCsrfToken.php
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        "*",
    ];
}
