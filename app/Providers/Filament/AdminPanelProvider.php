<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\DocumentTemplateResource;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\LeadResource;
use App\Filament\Resources\ProductResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use TomatoPHP\FilamentAlerts\FilamentAlertsPlugin;
use TomatoPHP\FilamentDocs\FilamentDocsPlugin;
use TomatoPHP\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use TomatoPHP\FilamentPlugins\FilamentPluginsPlugin;
use TomatoPHP\FilamentSaasPanel\FilamentSaasTeamsPlugin;
use TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;
use TomatoPHP\FilamentTypes\FilamentTypesPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
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
            ->brandLogoHeight('80px')
            ->font(
                'Readex Pro',
                provider: GoogleFontProvider::class,
            )
            ->profile()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->spa()
            ->authMiddleware([
                Authenticate::class,
            ]);

        $panel->plugin(FilamentUsersPlugin::make());
        $panel->plugin(FilamentLanguageSwitcherPlugin::make());
        $panel->plugin(
            FilamentAccountsPlugin::make()
                ->useAvatar()
                ->useExport()
                ->useImport()
                ->canLogin()
                ->canBlocked()
                ->useTypes()
                ->useImpersonate()
                ->impersonateRedirect('/app')
        );

        $panel->plugin(
            FilamentSaasTeamsPlugin::make()
                ->allowAccountTeamTableAction()
                ->allowAccountTeamTableBulkAction()
                ->allowAccountTeamFilter()
                ->allowAccountTeamFormComponent()
                ->allowAccountTeamTableColumn()
        );
        $panel->plugin(FilamentShieldPlugin::make());
        $panel->plugin(FilamentDocsPlugin::make());
        $panel->plugin(FilamentAlertsPlugin::make());
        $panel->plugin(FilamentTranslationsPlugin::make());
        $panel->plugin(FilamentSettingsHubPlugin::make());
        $panel->plugin(FilamentTypesPlugin::make());
        $panel->plugin(FilamentPluginsPlugin::make());

        $panel->navigation(function (NavigationBuilder $builder) {
            $builder->items(Dashboard::getNavigationItems());
            $builder->items(CustomerResource::getNavigationItems());
            $builder->items(LeadResource::getNavigationItems());
            $builder->items(CategoryResource::getNavigationItems());
            $builder->items(ProductResource::getNavigationItems());
            $builder->items(EmployeeResource::getNavigationItems());
            $builder->items(DocumentTemplateResource::getNavigationItems());

            return $builder;
        });

        return $panel;
    }
}
