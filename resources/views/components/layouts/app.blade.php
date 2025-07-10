<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Mymore' }}</title>
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
        <a id="whatsappBtn" href="https://wa.me/2348022008793" target="_blank" class="floating-btn bg-black">
            <i class="fab fa-whatsapp"></i>
        </a>

        <a id="callBtn" href="tel:+2348022888712" class="floating-btn bg-primary">
            <i class="fas fa-phone"></i>
        </a>
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