<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 280px; /* Default width */
            background-color: #343a40;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-top: 10px;
            padding-bottom: 10px;
            overflow-y: auto;
            transition: width 0.3s;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar .menu-title {
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: bold;
            border-bottom: 1px solid #495057;
            background-color: #343a40;
            position: relative;
        }

        .expand-collapse-btn {
            /* position: absolute;
            top: 50%;
            right: 0px; */
            /* transform: translateY(-50%); */
            background-color: #343a40;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            /* border-radius: 20%; */
        }

        .expand-collapse-btn:hover {
            background-color: #565d64;
        }

        .sidebar .menu-title2 {
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: bold;
            border-bottom: 1px solid #495057;
            background-color: #3e444a;
        }

        .sidebar .collapse a {
            font-size: 0.9rem;
            padding-left: 40px;
        }

        .sidebar .collapse a:hover {
            background-color: #565d64;
        }

        .content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 220px;
            }

            .sidebar.collapsed {
                width: 70px;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')

</head>
<body>
    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> --}}

    @stack('scripts')
</body>
</html>
