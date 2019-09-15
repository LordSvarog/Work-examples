<div class="home_bottom">
    <h3>Предыдущие версии новости</h3>
    @foreach ($backups as $backup)
        @if ($backup['data'] != $post['data'])
            <input class="hide" id="{!! $backup['id'] !!}" type="checkbox" >
            <label for="{!! $backup['id'] !!}">Версия от {!! $backup['created_at']->format('d-m-Y H:i:s') !!}</label>
            <div>
                <h2>{!! $backup['h1'] !!}</h2>
                {!! $backup['data'] !!}
                <a href="{{ route('post.recovery',['id'=> $backup['id'], 'id_posts'=> $backup['id_posts']]) }}" id="backup">Восстановить эту версию</a>
            </div>
            <br><br>
        @endif
    @endforeach
</div>