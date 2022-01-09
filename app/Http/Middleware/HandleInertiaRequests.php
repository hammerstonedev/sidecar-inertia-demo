<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $ziggy = new Ziggy($group = null, $request->url());
        $ziggy = $ziggy->toArray();

        // During development, send over the entire Ziggy object, so that
        // when routes change we don't have to redeploy.  In production,
        // only send the current URL, as we will bake the Ziggy config
        // into the Lambda SSR package.
        $ziggy = app()->environment('production') ? Arr::only($ziggy, 'url') : $ziggy;

        return array_merge(parent::share($request), [
            'ziggy' => $ziggy
        ]);
    }
}
