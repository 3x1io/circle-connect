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

        .break {
            page-break-before: always
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
                padding-left: 70px !important;
            }

            .footer {
                bottom: 0;
                padding-left: 70px !important;
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

<body style="padding-left: 60px !important;">
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
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<div class="content p-6" style="background-image: url('{{ url('background.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
    {{ $slot }}
</div>
<div class="footer">
    <div class="flex flex-col justify-center items-center">
        <img src="{{ url('footer.png') }}" style="width: 100%; height: auto;" />
    </div>
</div>
</body>

</html>
