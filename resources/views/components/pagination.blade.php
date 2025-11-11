@if ($paginator->hasPages())
    <nav class="pagination-container" role="navigation" aria-label="{{ __('تنقل الصفحات') }}">
        <div class="pagination-info">
            <span class="pagination-text">
                عرض {{ $paginator->firstItem() }} إلى {{ $paginator->lastItem() }} من {{ $paginator->total() }} نتيجة
            </span>
        </div>

        <ul class="pagination">
            {{-- السابق --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="{{ __('السابق') }}">
                    <span class="page-link" aria-hidden="true">
                        <i class="arrow">←</i>
                        <span class="sr-only">السابق</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" rel="prev" aria-label="{{ __('السابق') }}">
                        <i class="arrow">←</i>
                        <span class="sr-only">السابق</span>
                    </a>
                </li>
            @endif

            {{-- أرقام الصفحات --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $start = max(1, $currentPage - 2);
                $end = min($lastPage, $currentPage + 2);
            @endphp

            {{-- الصفحة الأولى --}}
            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->appends(request()->query())->url(1) }}">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- الصفحات المحيطة بالصفحة الحالية --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->appends(request()->query())->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- الصفحة الأخيرة --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
            @endif

            {{-- التالي --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" rel="next" aria-label="{{ __('التالي') }}">
                        <span class="sr-only">التالي</span>
                        <i class="arrow">→</i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="{{ __('التالي') }}">
                    <span class="page-link" aria-hidden="true">
                        <span class="sr-only">التالي</span>
                        <i class="arrow">→</i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
    /* Enhanced Pagination Styles */
    .pagination-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        margin: 40px 0;
        padding: 20px;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 14px;
        text-align: center;
    }

    .pagination {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 8px;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }

    .page-item {
        margin: 0;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 16px;
        margin: 0;
        font-size: 14px;
        font-weight: 500;
        line-height: 1;
        text-decoration: none;
        color: #495057;
        background-color: #fff;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        min-width: 48px;
        min-height: 48px;
        position: relative;
        overflow: hidden;
    }

    .page-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: -1;
    }

    .page-link:hover {
        color: #fff;
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(1, 107, 97, 0.2);
    }

    .page-link:hover::before {
        opacity: 1;
    }

    .page-item.active .page-link {
        color: #fff;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-color: var(--primary);
        box-shadow: 0 6px 15px rgba(1, 107, 97, 0.3);
        font-weight: 600;
    }

    .page-item.disabled .page-link {
        color: #c0c4cc;
        background-color: #f8f9fa;
        border-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
    }

    .arrow {
        font-size: 16px;
        font-weight: bold;
        line-height: 1;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .pagination-container {
            margin: 30px 0;
            padding: 15px;
        }

        .pagination {
            gap: 4px;
        }

        .page-link {
            padding: 10px 12px;
            font-size: 13px;
            min-width: 40px;
            min-height: 40px;
        }

        .pagination-info {
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .pagination {
            gap: 2px;
        }

        .page-link {
            padding: 8px 10px;
            font-size: 12px;
            min-width: 36px;
            min-height: 36px;
        }

        /* إخفاء بعض أرقام الصفحات على الشاشات الصغيرة */
        .page-item:not(.active):not(:first-child):not(:last-child):not(:nth-child(2)):not(:nth-last-child(2)) {
            display: none;
        }
    }

    /* Animation for page transitions */
    .page-link {
        position: relative;
    }

    .page-link::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.3s ease;
    }

    .page-link:active::after {
        width: 100%;
        height: 100%;
    }

    /* Enhanced hover effects */
    .pagination:hover .page-item:not(:hover) .page-link {
        opacity: 0.7;
        transform: scale(0.95);
    }

    .page-item:hover .page-link {
        opacity: 1 !important;
        transform: translateY(-2px) scale(1.05) !important;
    }
    </style>
@endif