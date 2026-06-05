<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>{{ config('app.name') }}</title>
    <link>{{ config('app.url') }}</link>
    <description>{{ $seo->meta_description ?: config('app.name') }}</description>
    <language>zh-CN</language>
    <atom:link href="{{ url('/feed') }}" rel="self" type="application/rss+xml" />
    @foreach($posts as $post)
    <item>
      <title>{{ $post->title }}</title>
      <link>{{ url('/blog/' . $post->slug) }}</link>
      <description>{{ $post->excerpt }}</description>
      @if($post->category)
      <category>{{ $post->category->name }}</category>
      @endif
      <pubDate>{{ $post->published_at?->toRssString() }}</pubDate>
      <guid>{{ url('/blog/' . $post->slug) }}</guid>
    </item>
    @endforeach
  </channel>
</rss>
