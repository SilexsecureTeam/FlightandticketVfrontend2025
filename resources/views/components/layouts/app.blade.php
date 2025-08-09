<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Mymore' }}</title>
    <style>
        .table-wrapper {
            display: flex;
            overflow-x: hidden;
            border: 1px solid #ddd;
            border-radius: 10px;
            position: relative;
            width: 75vw;
        }

        .fixed-column {
            background-color: #f0f8ff;
            flex: 0 0 140px;
            border-right: 1px solid #ddd;
        }

        .fixed-column .cell {
            font-weight: bold;
            text-align: left;
        }

        .scrollable-columns {
            display: flex;
            transition: transform 0.4s ease-in-out;
            min-width: 0;
        }

        .column {
            min-width: 150px;
            flex-shrink: 0;
            border-right: 1px solid #ddd;
            text-align: center;
            background-color: #fff;
        }

        .cell {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .header-cell {
            font-weight: bold;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .bg-red {
            background: #97050C;
        }

        .btn {
            background: #97050C;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .cell {
                font-size: 12px;
                padding: 10px;
            }

            .column {
                min-width: 160px;
            }

            .fixed-column {
                flex: 0 0 120px;
            }

            .table-wrapper {
                width: 96vw;
            }

            .btn {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .column {
                min-width: 140px;
            }
        }

        .nav-header {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid #ccc;
            background-color: #1a1a1a;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .btn.active {
            background-color: #c00;
            border-color: #c00;
            color: #fff;
        }

        .dropdown {
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #1a1a1a;
            padding: 8px 16px;
            font-size: 14px;
        }

        .passenger-box {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 12px;
        }

        .form-section {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }

        .form-box {
            /* flex: 1 1 200px; */
            /* background-color: #111;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 10px 14px; */
            /* position: relative; */
        }

        .form-box label {
            display: block;
            font-size: 12px;
            color: #ccc;
            margin-bottom: 4px;
        }

        .form-box .location {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-box .location span {
            font-weight: bolder;
            color: #fff;
        }

        .form-box .location i {
            margin-right: 6px;
        }

        .form-box .remove {
            background: none;
            border: none;
            color: #ccc;
            cursor: pointer;
            font-size: 12px;
        }

        .calendar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .calendar-icon {
            font-size: 20px;
            color: rgba(10, 10, 10, 1);
        }

        .search-btn {
            background-color: #c00;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
            font-size: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .displaynoe {
            display: unset !important;
        }

        @media (max-width: 600px) {
            .calendar {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-btn {
                margin-top: 10px;
            }

            .form-section {
                flex-direction: column;
            }

            .dispNonme {
                display: none !important;
            }

            .form-box .location span,
            .form-box label {
                font-size: 8px;
            }

            .form-box {
                padding: 5px 2px;
            }
        }

        /* Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 10001000;
        }

        .modal-overlay.active {
            display: block;
        }

        /* Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            max-width: 600px;
            width: 100%;
            background: white;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 100100000;
            overflow-y: auto;
        }

        .modal.active {
            transform: translateX(0);
        }

        .modal-header {
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }

        .modal-closeBtn {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            color: #888;
        }

        .modal-body {
            padding: 16px;
        }

        @media (max-width: 768px) {
            .modal {
                width: 100%;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="text-white  ">
    @livewire('partials.nav-bar')

    {{ $slot }}
    @livewireScripts

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />
    <!--Start of Tawk.to Script-->
    <div class="floating-btns d-flex flex-column align-items-end">
        <a id="whatsappBtn" href="https://wa.me/2348022008793" target="_blank" class="floating-btn bg-primary">
            <i class="fab fa-whatsapp"></i>
        </a>

        {{--<a id="callBtn" href="tel:+2348022888712" class="floating-btn bg-primary">
            <i class="fas fa-phone"></i>
        </a>--}}
    </div>
    {{--<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/667c78119d7f358570d3ac5c/1i1b3a1iu';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>--}}
    <!--End of Tawk.to Script-->
    <style>
        .floating-btns {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .floating-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            color: white;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .floating-btn+.floating-btn {
            margin-top: 12px;
        }
    </style>
</body>

</html>