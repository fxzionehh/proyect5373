<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [

  
            'flash' => [
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
            ],

            'auth' => [
                'user' => $request->user()
                    ? $request->user()->load('role')
                    : null,

                'permissions' => $request->user() && $request->user()->role
                    ? $request->user()->role->permissions->pluck('nombre')->toArray()
                    : [],
            ],
        ]);
    }
}