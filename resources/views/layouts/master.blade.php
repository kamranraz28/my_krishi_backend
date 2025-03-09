<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Phoenixcoded" />

    <title>My Krishi</title>

    @include('layouts.css') <!-- Include your CSS files here -->
</head>

<body>


    @include('header') <!-- Include your header here -->

    <div class="dashboard-container">
        @include('sidebar') <!-- Include your sidebar here -->

        <main class="dashboard-content">
            <div class="pcoded-main-container">
                <div class="pcoded-content">
                    @yield('content') <!-- Content will be injected here -->
                </div>
            </div>
        </main>
    </div>

    @include('layouts.js') <!-- Include your JS files here -->
</body>

</html>
