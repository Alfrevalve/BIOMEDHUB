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
                'primary' => Color::hex('#2281A7'),
                'success' => Color::hex('#359360'),
                'info' => Color::hex('#17404C'),
                'gray' => Color::hex('#0E252F'),
            ])
            ->renderHook(
                'panels::head.end',
                fn () => new HtmlString(
                    '<link rel="stylesheet" href="'.asset('css/filament/admin/theme.css').'">'
                )
            )
            ->renderHook(
                'panels::topbar.end',
                fn () => new HtmlString(
                    <<<HTML
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div id="bh-clock" style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:999px;background:rgba(15,107,182,0.08);color:#0f6bb6;font-weight:600;font-size:0.85rem;">
                            <span id="bh-date"></span>
                            <span aria-hidden="true">·</span>
                            <span id="bh-time"></span>
                        </div>
                        <div id="bh-msg-badge" style="display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:rgba(16,185,129,0.12);color:#0f5132;font-weight:600;font-size:0.85rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;">
                                <path d="M2.5 4.75A2.75 2.75 0 0 1 5.25 2h9.5A2.75 2.75 0 0 1 17.5 4.75v7.5A2.75 2.75 0 0 1 14.75 15H6.707l-2.48 2.48A1 1 0 0 1 3 16.914V4.75Z" />
                            </svg>
                            <span id="bh-msg-count">--</span>
                        </div>
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

                            const msgEl = document.getElementById('bh-msg-count');
                            if (msgEl) {
                                const badge = document.body?.dataset?.unreadMessages;
                                msgEl.textContent = badge !== undefined ? badge : '';
                            }
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
