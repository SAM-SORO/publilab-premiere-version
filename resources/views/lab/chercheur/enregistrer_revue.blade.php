@extends('baseChercheur')

@section('content')

<div id="content" class="p-4 p-md-5 pt-5 mt-4">
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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="col-10 mx-auto form-container shadow p-5 mb-5 bg-body rounded">
        <h2 class="mb-5 text-center" style="color: #2a52be;">Enregistrer une Revue</h2>
        <form action="{{ route('chercheur.enregistrer-revue') }}" method="POST">
            @csrf
            <div class="form-group row mb-4">
                <label for="cod_ISSN" class="col-sm-3 col-form-label">Code ISSN</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="cod_ISSN" name="cod_ISSN" required>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label for="cod_DOI" class="col-sm-3 col-form-label">Code DOI</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="cod_DOI" name="cod_DOI" required>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label for="editeur" class="col-sm-3 col-form-label">Editeur</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="editeur" name="editeur">
                </div>
            </div>
            <div class="form-group row mb-4">
                <label for="titre" class="col-sm-3 col-form-label">Titre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="titre" name="titre">
                </div>
            </div>
            <div class="form-group row mb-4">
                <label for="indexe" class="col-sm-3 col-form-label">Indexé</label>
                <div class="col-sm-9">
                    <select class="form-control" id="indexe" name="indexe">
                        <option value="1">Oui</option>
                        <option value="0">Non</option>
                    </select>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label for="organisme_index" class="col-sm-3 col-form-label">Organisme d'indexation</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="organisme_index" name="organisme_index">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mx-auto d-block">Enregistrer</button>
        </form>
    </div>
</div>

@endsection
