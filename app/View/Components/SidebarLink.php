<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarLink extends Component
{
    public $route;
    public $icon;
    public $label;
    public $active;
    public $roles;

    public function __construct($route, $icon, $label, $roles = [])
    {
        $this->route = $route;
        $this->icon = $icon;
        $this->label = $label;
        $this->roles = $roles;

        $this->active = request()->routeIs($route);
    }

    public function canShow()
    {
        if (empty($this->roles)) {
            return true;
        }

        return auth()->check() &&
            auth()->user()->hasAnyRole($this->roles);
    }

    public function render(): View|Closure|string
    {
        return view('components.sidebar-link');
    }
}