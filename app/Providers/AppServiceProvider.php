<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Password defaults
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        // Production URL settings
        if (config('app.env') === 'production') {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');

            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $_SERVER['HTTPS'] = 'on';
            }

            if (Request::getHost() === 'itdportfolio-laravel.blog') {
                header("Location: https://www.itdportfolio-laravel.blog" . Request::getRequestUri(), true, 301);
                exit;
            }
        }

        // Nonce directive for CSP
        Blade::directive('nonce', function () {
            return '<?php echo "nonce=\"" . ($cspNonce ?? "") . "\""; ?>';
        });
    }
}