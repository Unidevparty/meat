<{!!'?xml version="1.0" encoding="utf-8"?'!!}>
<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0"
>
    <channel>
    <title>{{ $title }}</title>
    <link>https://meat-expert.ru/</link>
    <description>{{ $description }}</description>
    <yandex:analytics type="Yandex" id=""></yandex:analytics>
        @foreach ($feed as $item)
        <item turbo="true">
            <title>{{ $item['title'] }}</title>
            <yandex:full-text>
                <![CDATA[
                    {!! $item['text'] !!}
                ]]>
            </yandex:full-text>

            <turbo:content>
                <![CDATA[
                    <figure>
                        <img src="{{ $item['image'] }}" />
                        <figcaption>{{ $item['title'] }}</figcaption>
                    </figure>
                    {!! $item['text'] !!}
                ]]>
            </turbo:content>
            


            <description>{{ $item['description'] }}</description>
            <enclosure url="{{ $item['image'] }}"/>
            <link>{{ $item['link'] }}</link>
            @if (!empty($item['author']))
               <author>{{ $item['author'] }}</author>
            @endif
            <category>{{ $item['category'] }}</category>
            <pubDate>{{ $item['date'] }}</pubDate>
        </item>
    @endforeach
    </channel>
</rss>