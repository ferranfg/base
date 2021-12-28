@component('mail::message')
<style>
    li {
        font-size: 16px;
        line-height: 1.5em;
    }
</style>

<h1 style="font-size:24px"><a href="{{ $post->canonical_url }}" style="text-decoration:none;color:#3d4852">{{ $post->name }}</a></h1>

@if ($post->author)
<table style="margin:1em 0">
    <tr>
        <td><img src="{{ $post->author->photo_url }}" style="border:none!important;border-radius:50%;display:block;height:45px;vertical-align:middle;width:45px" /></td>
        <td>
            <div style="line-height:1em;margin-left:12px">
                <div style="font-size:14px;line-height:17px;height:17px;margin-bottom:4px">{{ $post->author->name }}</div>
                <div style="font-size:12px;color:rgb(118,118,118)">
                    <time datetime="{{ $post->updated_at }}">{{ $post->updated_at->format('M d') }}</time>
                </div>
            </div>
        </td>
    </tr>
</table>
@endif

*{{ $post->excerpt }}*

@if ($post->imagekit_url)
<img src="{{ $post->imagekit_url }}" alt="{{ $post->name }}" />
@endif

<hr style="background: rgba(204, 204, 204, 0.6); border: none; height: 1px; margin: 24px 0px; padding: 0px;" />

@basedown($post->content)

@component('mail::subcopy')
[Unsubscribe]({{ url("newsletter/unsubscribe/{$token}") }})
@endcomponent
@endcomponent