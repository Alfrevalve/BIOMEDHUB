@props([
    'createUrl',
    'indexUrl',
    'reportesUrl' => null,
])

<div id="bh-cirugia-actions" class="rounded-2xl border border-sky-100/60 bg-gradient-to-r from-sky-600 via-cyan-600 to-emerald-500 text-white shadow-xl">
    <style>
        #bh-cirugia-actions .bh-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 36px;
            padding: 0 12px;
            border-radius: 999px;
            background: #0dbdf0;
            color: #0b2f3b;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }
        #bh-cirugia-actions .bh-btn:hover {
            background: #0cc0f5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        #bh-cirugia-actions .bh-btn:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 2px;
        }
        #bh-cirugia-actions .bh-btn svg {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
        }
    </style>
    <div class="flex flex-col gap-2 px-6 py-3">
        <div class="flex flex-wrap items-center gap-2 lg:flex-nowrap">
            <a href="{{ $createUrl }}" class="bh-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" width="16" height="16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5" />
                </svg>
                <span>Nueva cirugia</span>
            </a>
            <a href="{{ $indexUrl }}" class="bh-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" width="16" height="16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h5m-8-7h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                </svg>
                <span>Ver agenda</span>
            </a>
            @if($reportesUrl)
                <a href="{{ $reportesUrl }}" class="bh-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" width="16" height="16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5h7l5 5v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5v4a1 1 0 0 0 1 1h4" />
                    </svg>
                    <span>Reportes</span>
                </a>
            @endif
        </div>
    </div>
</div>
