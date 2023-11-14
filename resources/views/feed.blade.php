@php
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . PHP_EOL;
@endphp
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
    <channel>
        <title>{{ $meta['title'] }}</title>
        <atom:link href="{{ url('feed') }}" rel="self" type="application/rss+xml" />
        <link>{{ url(config('app.url')) }}</link>
        <description>{{ $meta['description'] }}</description>
        <language>{{ $meta['language'] }}</language>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <lastBuildDate>{{ $meta['updated'] }}</lastBuildDate>

        @foreach($items as $item)
            <item>
                <title>{{ $item->title }}</title>
                <link>{{ url($item->link) }}</link>
                <description><![CDATA[<img src="{{ $item->image }}" alt="{{ $item->title }}" />{{ $item->summary }}]]></description>
                <content:encoded><![CDATA[<img src="{{ $item->image }}" alt="{{ $item->title }}" />@basedown($item->content)]]></content:encoded>
                <dc:creator><![CDATA[{{ $item->authorName }}]]></dc:creator>
                <guid isPermaLink="false">{{ url($item->id) }}</guid>
                <pubDate>{{ $item->updated->toRssString() }}</pubDate>
                @foreach($item->category as $category)
                    <category>{{ $category }}</category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
