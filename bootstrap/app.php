->withMiddleware(function ($middleware) {
    $middleware->alias(['admin' => \App\Http\Middleware\EnsureAdmin::class]);
})