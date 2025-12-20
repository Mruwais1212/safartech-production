<?php

namespace App\Providers;

use App\Models\Panel\Contact;
use App\Models\Panel\Privilege;
use App\Models\Panel\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class PanelServiceProvider extends ServiceProvider
{
    public function register()
    {
        View::composer(
            ['admin.layout'],
            function ($view) {
                $contacts_count = Contact::where('status', 0)->count();

                $user_privileges = auth('admin')->user()->privileges()->pluck('privilege_id')->toArray();

                $parent_privileges = Privilege::where('parent_id', null)
                    ->whereHas(
                        'subPrivilege',
                        function ($query) use ($user_privileges) {
                            $query->whereIn('id', $user_privileges)->where('type', 1)->where('status', 1);
                        }
                    )->pluck('id')->toArray();

                $privileges = Privilege::with(
                    ['subPrivilegeWithPermission' => function ($query) use ($user_privileges) {
                        $query->whereIn('id', $user_privileges)->where('type', 1)->where('status', 1);
                    }]
                )
                    ->whereIn('id', array_merge($parent_privileges, $user_privileges))
                    ->where('parent_id', null)->orderBy('sort', 'asc')->where('type', 1)->where('guard', 1)->get();

                $settings = Setting::whereIn('code', ['website_name', 'admin_panel_color'])->pluck('value', 'code')->toArray();

                $background = $settings['admin_panel_color'];
                $title = $settings['website_name'];
                $view->with(['contacts_count' => $contacts_count, 'privileges' => $privileges, 'background' => $background, 'title' => $title]);
            }
        );

        View::composer(
            ['admin.auth.login', 'admin.footer'],
            function ($view) {
                $settings = Setting::whereIn('code', ['website_name', 'admin_panel_color'])->pluck('value', 'code')->toArray();

                $background = $settings['admin_panel_color'];
                $title = $settings['website_name'];
                $view->with(['background' => $background, 'title' => $title]);
            }
        );

        View::composer(
            'admin.*',
            function ($view) {
                if (auth('admin')->user() && ! in_array($view->getName(), [
                    'admin.layout', 'admin.footer', 'admin.message', 'admin.auth.login', 'admin.panel.components.addjs',
                    'admin.panel.components.datatablejs', 'admin.backend.work-request.action_taken',
                ])) {
                    $view->with('my_permissions', auth('admin')->user()->privileges()->pluck('code')->toArray());
                }
            }
        );
    }

    public function boot() {}
}
