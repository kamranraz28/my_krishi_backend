<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $term->title }}</title>
    <style>
        body {
            background: #eee;
            padding: 2rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background: white;
            padding: 30mm 20mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: #333;
            box-sizing: border-box;
        }

        .project-name {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #27ae60; /* a fresh green */
            margin-bottom: 0.3rem;
            font-family: 'Segoe UI Black', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .content-body p {
            margin-bottom: 1rem;
            line-height: 1.75;
            font-size: 15px;
        }

        .content-body ul {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .content-body li {
            margin-bottom: 0.5rem;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .a4-page {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="a4-page">
        <div class="project-name">MyKrishi</div>
        <h2>{{ $term->title }}</h2>
        <div class="content-body">
            {!! $term->content !!}
        </div>
    </div>
</body>
</html>
