<div class="interview-card search-card">
    <header>
        <div class="type">Компании</div>
    </header>

    <figure>
        <a href="{{ route('company.show', $searchable->alias) }}">
            <img src="{{ resize($searchable->logo, 186, 186, false) }}" alt="{{ $searchable->name }}">
        </a>
    </figure>

    <div class="interview-card-description">
        <h3><a href="{{ route('company.show', $searchable->alias) }}">{{ $searchable->name }}</a></h3>
        <p>{{ $searchable->introtext }}</p>
        <footer>
            @if ($searchable->types)
                <div class="h">Профиль компании:</div>
                <ul class="tag-list">
                    @foreach ($searchable->types as $type)
                        <li><a href="{{ route('company.list', ['profiles' => [$type->id]]) }}">{{ $type->name }}</a></li>
                    @endforeach
                </ul>
            @endif
        </footer>
    </div>
</div>
