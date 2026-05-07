<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="_token" content="{{ csrf_token() }}" />

    <title>Vaak golvslip MX</title>

    <!-- App favicon -->
    <!-- <link rel="novapay-pos icon" href="{{ asset('assets/images/favicon.ico') }}"> -->
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/tui-date-picker/tui-date-picker.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href='https://cdn.boxicons.com/3.0.7/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/plugin.js') }}"></script> --}}
    @yield('css')
</head>

<body data-sidebar="dark">
    @auth
        <div id="layout-wrapper">
            @include('layouts.header')
            @include('layouts.left-sidebar')
        </div>
    @endauth
    @if ($errors->any())
        @include('components.alerts.error-alert', ['message' => $errors->all()])
    @endif
    @error('error')
        <div class="col-6">
            @include('components.alerts.error-alert', ['message' => $message])
        </div>
    @enderror
    @if(session('success'))
        <div class="col-6">
            @include('components.alerts.success-alert', ['message' => session('success')])
        </div>
    @endif
    @include('components.loader');
    @yield('content')
    @auth
        @include('layouts.footer')
    @endauth
    {{-- @include('layouts.right-sidebar') --}}

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>


    <!-- apexcharts -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard-blog.init.js') }}"></script>


    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- App js -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pages/form-file-upload.init.js') }}"></script> --}}
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        function markAsRead(event, id, url) {
            event.preventDefault(); // stop immediate navigation

            fetch("{{ url('/admin/notifications/read') }}/" + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to mark notification as read');
                    }
                    return response.json();
                })
                .then(() => {
                    window.location.href = url; // redirect AFTER ajax
                })
                .catch(error => {
                    console.error(error);
                    window.location.href = url; // fallback redirect
                });
        }
    </script>
    @yield('js')
</body>

</html>