<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>{{ env('APP_NAME') }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  @stack('stylesheet')
</head>
<body>
<div id="app"></div>
<script src="{{ mix('js/main.js') }}"></script>
@stack('javascript')
  <script>
  (function (m, a, z, e) {
    var s, t;
    try {
      t = m.sessionStorage.getItem('maze-us');
    } catch (err) {}

    if (!t) {
      t = new Date().getTime();
      try {
        m.sessionStorage.setItem('maze-us', t);
      } catch (err) {}
    }

    s = a.createElement('script');
    s.src = z + '?apiKey=' + e;
    s.async = true;
    a.getElementsByTagName('head')[0].appendChild(s);
    m.mazeUniversalSnippetApiKey = e;
  })(window, document, 'https://snippet.maze.co/maze-universal-loader.js', '7703da8d-cc4a-4e79-a4fa-daafd979b1f9');
</script>
</body>
</html>
