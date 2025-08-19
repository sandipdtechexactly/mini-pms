<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class UtilityServiceProvider extends ServiceProvider
{
    /**
     * The path to the "helpers" directory.
     *
     * @var string
     */
    protected $helpersPath;

    /**
     * Create a new service provider instance.
     *
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->helpersPath = app_path('Utils/helpers.php');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the helpers file
        if (file_exists($this->helpersPath)) {
            require_once $this->helpersPath;
        }

        $this->registerRepositories();
        $this->registerServices();
    }

    /**
     * Register the application's repositories.
     *
     * @return void
     */
    protected function registerRepositories()
    {
        $this->app->bind(
            \App\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Interfaces\TaskRepositoryInterface::class,
            \App\Repositories\TaskRepository::class
        );
        
        // Add other repository bindings here
        // $this->app->bind(
        //     \App\Repositories\Interfaces\ProjectRepositoryInterface::class,
        //     \App\Repositories\ProjectRepository::class
        // );
    }

    /**
     * Register the application's services.
     *
     * @return void
     */
    protected function registerServices()
    {
        $this->app->bind(\App\Services\UserService::class, function ($app) {
            return new \App\Services\UserService(
                $app->make(\App\Repositories\Interfaces\UserRepositoryInterface::class)
            );
        });
        
        $this->app->bind(\App\Services\TaskService::class, function ($app) {
            return new \App\Services\TaskService(
                $app->make(\App\Repositories\Interfaces\TaskRepositoryInterface::class)
            );
        });
        
        // Add other service bindings here
        // $this->app->bind(\App\Services\ProjectService::class, function ($app) {
        //     return new \App\Services\ProjectService(
        //         $app->make(\App\Repositories\Interfaces\ProjectRepositoryInterface::class)
        //     );
        // });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register custom Blade directives
        $this->registerBladeDirectives();

        // Register custom validators
        $this->registerValidators();
    }

    /**
     * Register custom Blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        // Example: @admin
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->hasRole('admin');
        });

        // Example: @manager
        Blade::if('manager', function () {
            return auth()->check() && auth()->user()->hasRole('manager');
        });

        // Example: @developer
        Blade::if('developer', function () {
            return auth()->check() && auth()->user()->hasRole('developer');
        });

        // Example: @role('admin')
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        // Example: @endrole
        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        // Example: @permission('edit-users')
        Blade::directive('permission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission({$permission})): ?>";
        });

        // Example: @endpermission
        Blade::directive('endpermission', function () {
            return '<?php endif; ?>';
        });
    }

    /**
     * Register custom validators.
     *
     * @return void
     */
    protected function registerValidators()
    {
        // Example of a custom validator
        // You can use it like: 'field' => 'custom_validation_rule'
        \Validator::extend('custom_validation_rule', function ($attribute, $value, $parameters, $validator) {
            // Your validation logic here
            return true; // or false based on validation
        });
    }
}
