@extends('baseChercheur')

@section('content')

    <div class="container mt-2">
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

        <!-- Affichage des messages d'erreur de validation -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>

    <div class="m-auto rounded shadow mb-5 bg-white p-4" style="width: 1100px;">
        <div class="m-auto rounded shadow-sm bg-white p-4 d-flex justify-content-around align-items-center mb-4">
            <h1 class="text-primary mb-0">Indiquer les articles inclus dans une revue</h1>
            {{-- <a href="{{ route('chercheur.index') }}" class="btn btn-info">RETOUR</a> --}}
        </div>

        <div style="display: flex; justify-content:center; align-items:center">

            <form action="{{ route('chercheur.enregistrer-association-article-revue') }}" method="POST" class="text-left w-75">
                @csrf

                <div class="mb-3 mt-4">
                    <label for="client_id" class="form-label">Revue</label>
                    <select name="client_id" id="client_id" class="form-control" required style="border: 2px solid gray">
                        @foreach($revues as $revue)
                            <option value="{{ $revue->id }}">{{ $revue->titre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sélection des articles inclus dans la revue avec informations spécifiques -->
                <div class="mb-3" id="articles_section">
                    <label class="form-label">Articles inclus</label>
                    <div id="articles">
                        <button type="button" class="btn btn-secondary mb-2" onclick="addArticleField()">Ajouter un article</button>
                        <div id="articleFields">
                            <div class="article-field mb-2">
                                <select name="articles[]" class="form-control mb-2" required>
                                    <option value="" disabled selected>Choisir un article</option>
                                    @foreach($articles as $article)
                                        <option value="{{ $article->id }}">{{ $article->titre }}</option>
                                    @endforeach
                                </select>
                                <div class="specific-info">
                                    <input type="text" class="form-control mb-2" name="specific_info[1][PageDebut]" placeholder="Page de début">
                                    <input type="text" class="form-control mb-2" name="specific_info[1][PageFin]" placeholder="Page de fin">
                                    <input type="date" class="form-control mb-2" name="specific_info[1][DatePublication]" placeholder="Date de publication">
                                    <input type="text" class="form-control mb-2" name="specific_info[1][Volume]" placeholder="Volume">
                                    <input type="text" class="form-control mb-2" name="specific_info[1][Numero]" placeholder="Numéro">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary col-span-4 mb-5 mt-3 w-24">Enregistrer</button>
            </form>
        </div>
    </div>

    <script>
        function addArticleField() {
            const articleFields = document.getElementById('articleFields');
            const newField = document.createElement('div');
            newField.classList.add('article-field', 'mb-2');
            newField.innerHTML = `
                <select name="articles[]" class="form-control mb-2" required>
                    <option value="" disabled selected>Choisir un article</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}">{{ $article->titre }}</option>
                    @endforeach
                </select>
                <div class="specific-info">
                    <input type="text" class="form-control mb-2" name="specific_info[${Math.random()}][PageDebut]" placeholder="Page de début">
                    <input type="text" class="form-control mb-2" name="specific_info[${Math.random()}][PageFin]" placeholder="Page de fin">
                    <input type="text" class="form-control mb-2" name="specific_info[${Math.random()}][DatePublication]" placeholder="Date de publication">
                    <input type="text" class="form-control mb-2" name="specific_info[${Math.random()}][Volume]" placeholder="Volume">
                    <input type="text" class="form-control mb-2" name="specific_info[${Math.random()}][Numero]" placeholder="Numéro">
                </div>
            `;
            articleFields.appendChild(newField);
        }
    </script>

@endsection
