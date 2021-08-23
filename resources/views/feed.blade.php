<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>{{ $meta['title'] }}</title>
        <link>{{ url(config('app.url')) }}</link>
        <description>{{ $meta['description'] }}</description>
        <language>{{ $meta['language'] }}</language>
        <pubDate>{{ $meta['updated'] }}</pubDate>

        @foreach($items as $item)
            <item>
                <title>{{ $item->title }}</title>
                <link>{{ url($item->link) }}</link>
                <description><![CDATA[@basedown($item->summary)]]></description>
                <author><![CDATA[{{ $item->author }}]]></author>
                <guid isPermaLink="false">{{ url($item->id) }}</guid>
                <pubDate>{{ $item->updated->toRssString() }}</pubDate>
                @foreach($item->category as $category)
                    <category>{{ $category }}</category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
