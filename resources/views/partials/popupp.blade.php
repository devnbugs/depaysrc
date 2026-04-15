<link rel="stylesheet" href="{{ asset('assets/global/css/iziToast.min.css') }}">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>
@if(session()->has('popupp'))
    @foreach(session('popupp') as $msg)
        <script>
            iziToast.show({
                title: "Hello!!!",
                message: "{{ __($msg[1]) }}",
                position: "center",
                timeout: 3000,
                color: "{{ $msg[0] === 'success' ? 'green' : 'red' }}",
            });
        </script>
    @endforeach
@endif

@if ($errors->any())
    @php
        $collection = collect($errors->all());
        $errors = $collection->unique();
    @endphp

    @foreach ($errors as $error)
        <script>
            iziToast.show({
                title: "OOPS!!!!",
                message: "{{ __($error) }}",
                position: "center",
                timeout: 3000,
                color: "red",
            });
        </script>
    @endforeach
@endif
