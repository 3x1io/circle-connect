<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/css" media="print">
        @page {
            size: A4;
            margin: 0;
        }
        table {
            width: 100%;
        }

        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: transparent;
        }

        .header, .footer {
            display: none; /* Hide header and footer by default */
            background: transparent;
        }

        .content {
            padding: 20px;
        }

        /* Print-specific styles */
        @media print {
            .header, .footer {
                display: block;
                position: fixed;
                left: 0;
                right: 0;
                background-color: #ffffff;
                text-align: center;
                padding: 10px;
            }

            .header {
                top: 0;
            }

            .footer {
                bottom: 0;
            }

            .content {
                margin-top: 0; /* Adjust based on header height */
                margin-bottom: 0; /* Adjust based on footer height */
            }

            @page {
                margin: 0; /* Adjust based on header and footer height */
            }
        }

    </style>
</head>

<body>
<div class="header mb-4">
    <div class="flex flex-col justify-end items-end border-black">
        <div class="flex justify-end p-4">
            <img width="60%" src="{{ url('storage/'.setting('site_logo')) }}" />
        </div>
    </div>
    <div class="mt-2 flex flex-col justify-start items-start border-black">
        <div class="flex justify-start p-4">
            <img width="60%" src="{{ url('top-sign.png') }}" style="margin-left: -10px"/>
        </div>
    </div>
</div>
<div class="content p-4" style="background-image: url('{{ url('background.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
    {{ $slot }}
</div>
<div class="footer">
    <div style="font-size: 12px; color: #926128 !important;" class="flex justify-between gap-4  p-4 my-4 text-start w-full h-full" >
        <div class="flex flex-col justify-start w-full">
            <div>
                <img src="{{ url('site-sign.png') }}" alt="" width="60%" style="margin-left: -5px; margin-bottom: 5px"/>
            </div>
            <div>Geschäftsführer: Atef Israil</div>
            <div>Klosterstraße 45 · 40211 Düsseldorf</div>
        </div>
        <div class="flex flex-col justify-start w-full">
            <div>Phone +49 (0) 211 58 34 62 00</div>
            <div>Fax +49 (0) 211 58 34 62 20</div>
            <div>info@chateau-royal.de</div>
            <div>www.chateau-royal.de</div>
        </div>
        <div class="flex flex-col justify-start w-full">
            <div>Sparkasse Neuss</div>
            <div>Kto-Nr.: 93345460</div>
            <div>BLZ: 30550000</div>
            <div>IBAN: DE 33305500000093345460</div>
            <div>SWIFT: WELA DE DN</div>
        </div>
        <div class="flex flex-col justify-start w-full">
            <div>Volksbank Düsseldorf Neuss eG</div>
            <div>Kto-Nr.: 2106733018</div>
            <div>BLZ: 30160213</div>
            <div>IBAN: DE 92301602132106733018</div>
            <div>SWIFT: GENO DE D1 DNE</div>
        </div>
        <div class="flex flex-col justify-start w-full">
            <div>Registergericht:</div>
            <div>AG Düsseldorf</div>
            <div>HRB 60688</div>
            <div>Ust-ID: DE 264306722</div>
        </div>
    </div>
</div>
</body>

</html>
