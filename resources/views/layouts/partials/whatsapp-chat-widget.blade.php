@php
    $whatsAppNumberRaw = config('services.whatsapp.number');
    $whatsAppNumber = preg_replace('/\D+/', '', (string) $whatsAppNumberRaw);
@endphp

@if(!empty($whatsAppNumber))
    <style>
        .whatsapp-chat-widget {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 100000;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background-color: #25d366;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(37, 211, 102, 0.45);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .whatsapp-chat-widget:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(37, 211, 102, 0.5);
        }

        .whatsapp-chat-widget:focus-visible {
            outline: 3px solid rgba(37, 211, 102, 0.35);
            outline-offset: 3px;
        }

        @media (max-width: 576px) {
            .whatsapp-chat-widget {
                right: 16px;
                bottom: 16px;
                width: 52px;
                height: 52px;
                font-size: 26px;
            }
        }
    </style>

    <a
        href="https://wa.me/{{ $whatsAppNumber }}"
        class="whatsapp-chat-widget"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat with us on WhatsApp"
        title="Chat with us on WhatsApp"
    >
        <i class="bi bi-whatsapp" aria-hidden="true"></i>
    </a>
@endif
