<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Widgets\CirugiaQuickActions;
use App\Filament\Widgets\CirugiasProximas;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\InstrumentistaStats;
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
            ->login(Login::class)
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
                function () {
                    $user = auth()->user();
                    $name = $user?->name ?? 'Usuario';
                    $avatar = null;

                    if ($user && method_exists($user, 'getFilamentAvatarUrl')) {
                        $avatar = $user->getFilamentAvatarUrl();
                    }

                    if (! $avatar) {
                        $avatar = $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($name);
                    }

                    $html = <<<HTML
<div class="bh-topbar" style="margin-left:auto;display:flex;align-items:center;gap:12px;">
    <div class="bh-topbar__icons" style="display:flex;align-items:center;gap:10px;">
        <button type="button" class="bh-chip" data-chip="noti" title="Notificaciones" aria-label="Notificaciones" style="position:relative;width:46px;height:46px;border-radius:50%;border:1px solid #e5e8f0;background:#fff;color:#1f2937;display:grid;place-items:center;box-shadow:0 6px 12px rgba(0,0,0,0.08);">
            <span class="bh-chip__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" style="width:22px;height:22px;"><path stroke="currentColor" stroke-width="1.7" d="M7.5 17h9m-9 0h-1A2.5 2.5 0 0 1 4 14.5v-.073a2.5 2.5 0 0 1 .638-1.686l.824-.907a3 3 0 0 0 .756-2.003V9a4.78 4.78 0 0 1 3.46-4.576l.322-.089a3 3 0 0 1 1.618 0l.322.089A4.78 4.78 0 0 1 15.78 9v.831a3 3 0 0 0 .755 2.003l.824.907A2.5 2.5 0 0 1 18 14.427V14.5A2.5 2.5 0 0 1 15.5 17h-8Z"/></svg>
            </span>
            <span class="bh-chip__badge" id="bh-chip-noti" style="position:absolute;top:-4px;right:-4px;min-width:20px;height:20px;padding:0 5px;border-radius:999px;background:#16a34a;color:#fff;font-size:11px;font-weight:700;display:grid;place-items:center;border:2px solid #fff;">0</span>
        </button>
        <button type="button" class="bh-chip" data-chip="msg" title="Mensajes" aria-label="Mensajes" style="position:relative;width:46px;height:46px;border-radius:50%;border:1px solid #e5e8f0;background:#fff;color:#1f2937;display:grid;place-items:center;box-shadow:0 6px 12px rgba(0,0,0,0.08);">
            <span class="bh-chip__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" style="width:22px;height:22px;"><path stroke="currentColor" stroke-width="1.7" d="M6.75 9.75h10.5M6.75 13.5h5.25M7.8 18h8.4a2.1 2.1 0 0 0 2.1-2.1V8.1A2.1 2.1 0 0 0 16.2 6H7.8a2.1 2.1 0 0 0-2.1 2.1v7.8A2.1 2.1 0 0 0 7.8 18Z"/></svg>
            </span>
            <span class="bh-chip__badge" id="bh-chip-msg" style="position:absolute;top:-4px;right:-4px;min-width:20px;height:20px;padding:0 5px;border-radius:999px;background:#16a34a;color:#fff;font-size:11px;font-weight:700;display:grid;place-items:center;border:2px solid #fff;">0</span>
        </button>
        <button type="button" class="bh-chip" data-chip="gift" title="Reportes / regalos" aria-label="Reportes" style="position:relative;width:46px;height:46px;border-radius:50%;border:1px solid #e5e8f0;background:#fff;color:#1f2937;display:grid;place-items:center;box-shadow:0 6px 12px rgba(0,0,0,0.08);">
            <span class="bh-chip__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" style="width:22px;height:22px;"><path stroke="currentColor" stroke-width="1.7" d="M5 11h14M12 11v10M5 11v7a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3v-7M5 11V8a3 3 0 0 1 3-3h2.5a.75.75 0 0 1 .75.75V11m0 0V5.75A.75.75 0 0 1 12.75 5H15a3 3 0 0 1 3 3v3"/><path stroke="currentColor" stroke-width="1.7" d="M7.5 5.5s.5-2 2.5-2 2.5 2 2.5 2"/></svg>
            </span>
            <span class="bh-chip__badge" id="bh-chip-gift" style="position:absolute;top:-4px;right:-4px;min-width:20px;height:20px;padding:0 5px;border-radius:999px;background:#16a34a;color:#fff;font-size:11px;font-weight:700;display:grid;place-items:center;border:2px solid #fff;">0</span>
        </button>
        <button type="button" class="bh-chip" data-chip="mode" title="Tema" aria-label="Tema" style="position:relative;width:46px;height:46px;border-radius:50%;border:1px solid #e5e8f0;background:#fff;color:#1f2937;display:grid;place-items:center;box-shadow:0 6px 12px rgba(0,0,0,0.08);">
            <span class="bh-chip__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" style="width:22px;height:22px;"><path stroke="currentColor" stroke-width="1.7" d="M12 3.75a8.25 8.25 0 1 0 8.25 8.25A8.25 8.25 0 0 0 12 3.75Zm0 0v16.5m8.25-8.25H3.75"/></svg>
            </span>
        </button>
    </div>
    <div class="bh-topbar__user" style="display:inline-flex;align-items:center;gap:10px;padding:6px 12px;border-radius:999px;background:#e8f8f0;color:#16a34a;font-weight:600;">
        <div class="bh-topbar__avatar" style="width:38px;height:38px;border-radius:50%;overflow:hidden;border:2px solid rgba(22,163,74,0.2);">
            <img src="{$avatar}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="bh-topbar__hello" style="display:flex;align-items:center;gap:4px;font-weight:600;">
            <span style="color:#1f2937;">Hello,</span>
            <strong style="color:#0f5132;">{$name}</strong>
        </div>
    </div>
</div>
<script>
    (() => {
        const dataset = document.body?.dataset || {};
        const setBadge = (id, fallback) => {
            const el = document.getElementById(id);
            if (el) {
                const key = id.replace('bh-chip-', '');
                const val = dataset[key] ?? fallback;
                el.textContent = val;
            }
        };
        setBadge('bh-chip-noti', '0');
        setBadge('bh-chip-msg', dataset.unreadMessages ?? '0');
        setBadge('bh-chip-gift', '0');

        const go = (path) => {
            if (path) {
                window.location.href = path;
            }
        };

        const noti = document.querySelector('.bh-chip[data-chip="noti"]');
        const msg = document.querySelector('.bh-chip[data-chip="msg"]');
        const gift = document.querySelector('.bh-chip[data-chip="gift"]');
        const mode = document.querySelector('.bh-chip[data-chip="mode"]');

        noti?.addEventListener('click', () => go('/admin/activity-log'));
        msg?.addEventListener('click', () => go('/admin/mensajes'));
        gift?.addEventListener('click', () => go('/admin/cirugia-reportes'));

        const applyMode = (theme) => {
            const isDark = theme === 'dark';
            document.documentElement.classList.toggle('bh-dark', isDark);
            const badge = document.querySelector('.bh-chip[data-chip="mode"] .bh-chip__badge');
            if (badge) {
                badge.textContent = isDark ? 'D' : 'L';
            }
        };

        const stored = localStorage.getItem('bh-mode');
        if (stored) {
            applyMode(stored);
        }

        mode?.addEventListener('click', () => {
            const next = document.documentElement.classList.contains('bh-dark') ? 'light' : 'dark';
            localStorage.setItem('bh-mode', next);
            applyMode(next);
        });
    })();
</script>
HTML;

                    return new HtmlString($html);
                }
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
                CirugiaQuickActions::class,
                DashboardStats::class,
                InstrumentistaStats::class,
                CirugiasProximas::class,
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

