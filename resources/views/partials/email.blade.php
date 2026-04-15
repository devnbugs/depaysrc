@php
    $hasHtmlDocument = stripos($body ?? '', '<html') !== false;
@endphp

@if($hasHtmlDocument)
    {!! $body !!}
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f7fb;">
    {!! $body !!}
</body>
</html>
@endif
