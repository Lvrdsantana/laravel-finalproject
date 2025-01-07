<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ... autres middlewares ...
        'coordinators' => \App\Http\Middleware\CheckCoordinatorRole::class,
        'teachers' => \App\Http\Middleware\CheckTeacherRole::class,
        'students' => \App\Http\Middleware\CheckStudentRole::class,
    ];
}
 
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ... autres middlewares ...
        'coordinators' => \App\Http\Middleware\CheckCoordinatorRole::class,
        'teachers' => \App\Http\Middleware\CheckTeacherRole::class,
        'students' => \App\Http\Middleware\CheckStudentRole::class,
    ];
} 