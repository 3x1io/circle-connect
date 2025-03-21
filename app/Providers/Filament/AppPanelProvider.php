<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Dashboard;
use App\Filament\App\Resources\LeadResource;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentAccounts\FilamentAccountsPlugin;
use TomatoPHP\FilamentSaasPanel\FilamentSaasPanelPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->id('app')
            ->path('app')
            ->databaseNotifications()
            ->unsavedChangesAlerts()
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/app.css')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->favicon(asset('favicon.ico'))
            ->brandName('Circle Connect')
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('40px')
            ->font(
                'Readex Pro',
                provider: GoogleFontProvider::class,
            )
            ->profile()
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

        $panel->plugin(
            FilamentSaasPanelPlugin::make()
                ->editTeam()
                ->deleteTeam()
                ->showTeamMembers()
                ->teamInvitation()
                ->allowTenants()
                ->checkAccountStatusInLogin()
                ->APITokenManager()
                ->editProfile()
                ->editPassword()
                ->browserSessionManager()
                ->deleteAccount()
                ->editProfileMenu()
                ->registration()
                ->useOTPActivation()
        );

        $panel->plugin(
            FilamentAccountsPlugin::make()
        );

        $panel->navigation(function (NavigationBuilder $builder) {
            $builder->items(Dashboard::getNavigationItems());
            $builder->items(LeadResource::getNavigationItems());

            return $builder;
        });

        return $panel;
    }
}
