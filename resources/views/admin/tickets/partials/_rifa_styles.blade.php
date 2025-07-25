@push('styles')
<style>
    .pulse-border {
        box-shadow: 0 0 0 2px #2563eb, 0 0 10px 2px #60a5fa;
        animation: pulse-border 1.5s infinite;
    }
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 2px #2563eb, 0 0 10px 2px #60a5fa; }
        50% { box-shadow: 0 0 0 6px #2563eb55, 0 0 20px 6px #60a5fa55; }
        100% { box-shadow: 0 0 0 2px #2563eb, 0 0 10px 2px #60a5fa; }
    }
    .blink {
        animation: blink-animation 1s steps(2, start) infinite;
        -webkit-animation: blink-animation 1s steps(2, start) infinite;
    }
    @keyframes blink-animation { to { visibility: hidden; } }
    .sticky-badge {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: 100;
        background: #2563eb;
        color: #fff;
        border-radius: 16px;
        padding: 18px 32px;
        box-shadow: 0 4px 32px #2563eb66;
        font-size: 1rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 14px;
    }
</style>
@endpush
