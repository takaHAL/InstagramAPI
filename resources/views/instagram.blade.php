<html>
  <head>
    <title>HelloWorld</title>
  </head>
  <body>
    @foreach ($mediaData as $media)
      <a href="{{$media['permalink']}}" target="_brank" rel="noopener">
        <img src="{{$media['media_url']}}" width="100px" alt="{{$media['caption']}}">
      </a>
    @endforeach
  </body>
</html>