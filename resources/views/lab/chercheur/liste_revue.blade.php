@extends('baseChercheur')

@section('content')
<div class="container">

    <div class="container mt-4">
        @if (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (Session::has('success'))
            <div class="alert alert-success" role="alert">
                <span>{{ Session::get('success') }}</span>
            </div>
        @endif
    </div>

    <div class="m-auto rounded shadow mb-5 bg-white p-4" style="width: 1100px;">
        <div class="flex flex-column p-4">
            <h2 class="mb-5 text-center" style="color: #2a52be;">LISTE DES REVUES</h2>

            <div class="m-auto rounded shadow-sm bg-white p-4 d-flex justify-content-between align-items-center mb-4">
                {{-- Formulaire de recherche --}}
                <div class="col-6">
                    <form action="{{ route('chercheur.rechercherRevue') }}" method="GET" class="d-flex mb-4">
                        @csrf
                        <input class="form-control me-2" type="search" name="search" placeholder="Recherche" aria-label="Search" style="border: 2px solid gray">
                        <button class="btn btn-outline-success" type="submit">Rechercher</button>
                    </form>
                </div>
                <form action="{{ route('chercheur.enregistrer-revue') }}" method="GET" class="float-end">
                    @csrf
                    <button type="submit" class="btn btn-primary">Ajouter une revue</button>
                </form>
            </div>

            <div class="col s12 mt-4">
                <table class="table table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>ISSN</th>
                            <th>DOI</th>
                            <th>Éditeur</th>
                            <th>Titre</th>
                            <th>Indexé</th>
                            <th>Organisme d'indexation</th>
                            <th>Articles associés</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        @foreach($revues as $revue)
                            <tr>
                                <td class="py-3" style="vertical-align: middle">{{ $revue->cod_ISSN }}</td>
                                <td class="py-3" style="vertical-align: middle">{{ $revue->cod_DOI }}</td>
                                <td class="py-3" style="vertical-align: middle">{{ $revue->editeur }}</td>
                                <td class="py-3" style="vertical-align: middle">{{ $revue->titre }}</td>
                                <td class="py-3" style="vertical-align: middle">{{ $revue->indexe ? 'Oui' : 'Non' }}</td>
                                <td class="py-3" style="vertical-align: middle">{{ $revue->organisme_index }}</td>
                                <td class="py-3" style="vertical-align: middle">
                                    @if ($revue->articles->isNotEmpty())
                                        <ul style="list-style-type: none; padding-left: 0;">
                                            @foreach ($revue->articles as $article)
                                                <li>{{ $article->titre }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        Aucun article associé
                                    @endif
                                </td>
                                <td class="py-3" style="vertical-align: middle">
                                    <form action="{{ route('chercheur.modifier-revue', $revue->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary px-3">Modifier</button>
                                    </form>
                                    <form action="{{ route('chercheur.supprimer-revue', $revue->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette revue ?')">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $revues->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
