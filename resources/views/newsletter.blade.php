@component('mail::message')
<style>
    li {
        font-size: 16px;
        line-height: 1.5em;
    }
</style>

# {{ $post->name }}

*{{ $post->excerpt }}*

<hr style="background: rgba(204, 204, 204, 0.6); border: none; height: 1px; margin: 24px 0px; padding: 0px;" />

@basedown($post->content)

@component('mail::subcopy')
[Unsubscribe]({{ url("newsletter/unsubscribe/{$token}") }})
@endcomponent
@endcomponent