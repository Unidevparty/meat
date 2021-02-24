<div class="interview-card company-card">
    <figure><a href="{{ route('company.show', $company->alias) }}"><img src="{{ resize($company->logo, 214, 214, false) }}" alt="{{ $company->name }}"></a></figure>
    <div class="interview-card-description">

        @if ($company->is_checked)
            <h3 class="is-valid">
                <a href="{{ route('company.show', $company->alias) }}">{{ $company->name }}</a>
                <span class="valid-sticker">Проверено</span>
            </h3>
        @else
            <h3><a href="{{ route('company.show', $company->alias) }}">{{ $company->name }}</a></h3>
        @endif

        <p>{{ $company->introtext }}</p>

        <footer>
            @if ($company->types)
                <div class="h">Профиль компании:</div>
                <ul class="tag-list">
                    @foreach ($company->types as $type)
                        <li><a href="{{ route('company.list', ['profiles' => [$type->id]]) }}">{{ $type->name }}</a></li>
                    @endforeach
                </ul>
            @endif

            @if ($company->updated_at && $company->is_checked)
                {{-- <div class="verification-date">Данные верны на {{ LocalizedCarbon::instance($company->updated_at)->formatLocalized('%d %f ‘%y') }}</div> --}}
                <div class="verification-date">Данные верны на {{ $company->updated_at->format('d.m.Y') }}</div>
            @endif

            <span class="stats">
                <span class="stats-unit">
                    <i class="icon icon-views"></i>
                    {{ $company->views }}
                </span>
            </span>
        </footer>
    </div>
</div>
