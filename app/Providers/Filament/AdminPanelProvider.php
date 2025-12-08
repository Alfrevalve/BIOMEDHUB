<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\CirugiasProximas;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\MovimientosActivos;
use App\Filament\Widgets\PedidosUrgentes;
use App\Filament\Widgets\StockBajo;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo.png'))
            ->colors([
                'primary' => Color::hex('#2281A7'),   // azul celeste del logo
                'success' => Color::hex('#359360'),  // verde azulado
                'info' => Color::hex('#17404C'),     // azul profundo
                'gray' => Color::hex('#0E252F'),     // petroleo oscuro para grises
            ])
            ->renderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString(
                    '<link rel="stylesheet" href="'.asset('css/filament/admin/theme.css').'">'
                )
            )
            ->renderHook(
                'panels::topbar.end',
                fn () => new HtmlString(
                    <<<HTML
                    <div id="bh-clock" style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:999px;background:rgba(15,107,182,0.08);color:#0f6bb6;font-weight:600;font-size:0.85rem;">
                        <span id="bh-date"></span>
                        <span aria-hidden="true">·</span>
                        <span id="bh-time"></span>
                    </div>
                    <script>
                        (() => {
                            const fmtDate = new Intl.DateTimeFormat('es-PE', { weekday:'short', day:'2-digit', month:'short', year:'numeric' });
                            const fmtTime = new Intl.DateTimeFormat('es-PE', { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false });
                            const dEl = document.getElementById('bh-date');
                            const tEl = document.getElementById('bh-time');
                            const tick = () => {
                                const now = new Date();
                                if (dEl) dEl.textContent = fmtDate.format(now);
                                if (tEl) tEl.textContent = fmtTime.format(now);
                            };
                            tick();
                            setInterval(tick, 1000);
                        })();
                    </script>
                    HTML
                )
            )
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                DashboardStats::class,
                PedidosUrgentes::class,
                MovimientosActivos::class,
                CirugiasProximas::class,
                StockBajo::class,
                AccountWidget::class,
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
    }
}
