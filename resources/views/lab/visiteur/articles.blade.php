@extends('baseVisite')

{{-- Titre de la page --}}
@section('title', 'Publilab-Articles')

@php
    $excludeFooter = true;
@endphp

{{-- Articles --}}
@section('contenue-main')
<div class="custom-article-height">
    <div class="container mt-5">
        <form class="form-inline justify-content-center my-2 mt-2" action="{{ route('recherche.article') }}" method="GET">
            @csrf
            <input class="form-control col-lg-8 col-6 col-sm-8 py-4" type="search" name="query" placeholder="Rechercher un article" aria-label="Rechercher">
            <button class="btn btn-primary search-btn ml-2" type="submit">Rechercher</button>
        </form>
    </div>

    <div class="container d-flex mt-5 align-items-center">
        <ul><li class="text-secondary col-5 ml-5 mt-4">Année</li></ul>
        <div class="d-flex col-10">
            <form action="{{ route('filtre.article') }}" method="GET" class="w-100">
                @csrf
                <select title="filtre par année" class="custom-select col-4 col-lg-2 col-sm-6 col-md-3" name="annee" onchange="this.form.submit()">
                    <option value="Tous">Filtre</option>
                    <option value="Tous">Tous</option>
                    @foreach ($annees as $annee)
                        <option value="{{ $annee }}">{{ $annee }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="p-5">
        @if ($articles->isEmpty())
            <div class="alert alert-info" role="alert">
                Aucun article n'a été publié.
            </div>
        @else
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @foreach ($articles as $article)
                <div class="col mb-4">
                    <div class="d-flex flex-column rounded shadow bg-white p-3 h-100">
                        <div class="d-flex">
                            @if ($article->documents->isNotEmpty())
                                <a href="#" class="d-flex">
                                    <img src="{{ asset('storage/' . $article->documents->first()->image) }}" class="img-fluid custom-img" width="150" height="150" alt="Image de l'article">
                                </a>
                            @else
                                <a href="#" class="d-flex">
                                    <img src="{{ asset('path/to/default_image.jpg') }}" class="img-fluid custom-img" alt="Image par défaut">
                                </a>
                            @endif
                            <div class="ml-3">
                                <a href="#" class="custom-link">
                                    <h5 class="text-danger">{{ $article->titre }}</h5>
                                </a>
                                <p class=" text-muted font-weight-bold">{{ $article->chercheur->nom }}</p> <!-- Remplacez par le champ approprié de votre modèle Article -->
                                <p>{{ $article->description }}
                                {{-- <p class="text-muted mt-3">{{ $article->created_at->format('Y') }}</p> --}}
                                <p class="text-muted mt-3"><small>Ajouté le {{ $article->created_at->format('d.m.Y') }}</small></p>

                                @if ($article->documents->isNotEmpty())
                                <p class="text-muted mt-3"><small>Document .{{ $article->documents->first()->format}}</small></p>
                                @endif
                            </div>


                        </div>
                        <div class="d-flex justify-content-end mt-auto">
                            @if ($article->documents->isNotEmpty())
                                @if(Auth::guard('visiteur')->check())
                                    <form action="{{ route('visiteur.telecharger-article', $article->documents->first()->id) }}" method="GET">
                                        <button type="submit" class="btn btn-info">Télécharger</button>
                                    </form>
                                @elseif(Auth::guard('chercheur')->check())
                                    <form action="{{ route('chercheur.telecharger-article', $article->documents->first()->id) }}" method="GET">
                                        <button type="submit" class="btn btn-info">Télécharger</button>
                                    </form>
                                @elseif(Auth::guard('admin')->check())
                                    <form action="{{ route('admin.telecharger-article', $article->documents->first()->id) }}" method="GET">
                                        <button type="submit" class="btn btn-info">Télécharger</button>
                                    </form>
                                @else
                                    <form action="{{ route('visiteur.telecharger-article', $article->documents->first()->id) }}" method="GET">
                                        <button type="submit" class="btn btn-info">Télécharger</button>
                                    </form>
                                @endif
                            @endif


                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $articles->links('vendor.pagination.bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<script>
    document.querySelector('.navbar').classList.add('bg-light');
    document.querySelector('.navbar').classList.add('custom-nav');
</script>
@endsection
