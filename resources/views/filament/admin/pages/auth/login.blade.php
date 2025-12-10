@php
    $heading = $this->getHeading();
    $subheading = $this->getSubheading();
@endphp

<div class="fi-simple-page bh-auth">
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_START, scopes: $this->getRenderHookScopes()) }}

    <div class="bh-auth__wrap">
        <div class="bh-auth__card">
            <div class="bh-auth__brand">
                <div class="bh-auth__logo">
                    <img src="{{ asset('images/logo.png') }}" alt="BiomedHub">
                </div>
                <p class="bh-auth__eyebrow">BiomedHub</p>
                <h2 class="bh-auth__title">
                    @if ($heading instanceof \Illuminate\Contracts\Support\Htmlable)
                        {!! $heading !!}
                    @else
                        {{ $heading }}
                    @endif
                </h2>
                <p class="bh-auth__hint">
                    @if (filled($subheading))
                        {!! $subheading !!}
                    @else
                        Ingresa con tu correo institucional para continuar.
                    @endif
                </p>
            </div>

            {{ $this->content }}
        </div>
    </div>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_END, scopes: $this->getRenderHookScopes()) }}
</div>
