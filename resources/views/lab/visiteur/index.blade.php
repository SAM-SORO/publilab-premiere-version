@extends('baseVisite')

{{-- titre de la page --}}
@section('title', 'Publilab')


@section('contenue-main')


<!-- Main landing page content -->
<section class="mt-5 mb-5">
    <div class="container custom-height mt-md-5 d-flex flex-column flex-md-row align-items-center">
        <!-- Texte à gauche -->
        <div class="text-section w-100 w-md-50 pr-md-5 mb-4 mb-md-0 mt-md-5">
            <h1 class="landing-header">PubliLab : Consulter, Publier et télécharger des articles</h1>

            <p class="landing-description">
                Publilab est une plateforme centralisée de l'INPHB dédiée à la publication d'articles de recherche des différents laboratoires ...
            </p>

            <!-- Bouton "Voir Les Publications" -->
            <a href="{{ route('visiteur.article') }}" class="btn btn-primary mt-3 text-center">Voir Les Publications</a>
        </div>

        <!-- Image à droite -->
        <div class="image-section w-100 w-md-50 text-center mb-md-0">
            <img src="{{ asset('assets/img/R.jpg') }}" alt="Image description" class="img-fluid animated-image">
        </div>
    </div>

</section>

<div style="margin-bottom: 130px;"></div>

<!-- Section pour afficher les cadres -->
<section class="container-fluid mt-5 mb-5 bg-white">

    <div class="container">
        <h3 class="row justify-content-center pt-5 pb-5">Articles publier recemment</h3>
        <div class="row justify-content-center">
            <!-- Cadre 1 -->
            <div class="col-md-6 mb-4 d-flex justify-content-center">
                <div class="card bg-white" style="width: 600px; height: 400px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Titre 1</h5>
                        <p class="card-text">Contenu du cadre 1...</p>
                        <a href="#" class="btn btn-primary mt-auto">Action</a>
                    </div>
                </div>
            </div>

            <!-- Cadre 2 -->
            <div class="col-md-6 mb-4 d-flex justify-content-center">
                <div class="card bg-white" style="width: 600px; height: 400px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Titre 2</h5>
                        <p class="card-text">Contenu du cadre 2...</p>
                        <a href="#" class="btn btn-primary mt-auto">Action</a>
                    </div>
                </div>
            </div>

            <!-- Cadre 3 -->
            <div class="col-md-6 mb-4 d-flex justify-content-center">
                <div class="card bg-white" style="width: 600px; height: 400px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Titre 3</h5>
                        <p class="card-text">Contenu du cadre 3...</p>
                        <a href="#" class="btn btn-primary mt-auto">Action</a>
                    </div>
                </div>
            </div>

            <!-- Cadre 4 -->
            <div class="col-md-6 mb-4 d-flex justify-content-center">
                <div class="card bg-white" style="width: 600px; height: 400px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Titre 4</h5>
                        <p class="card-text">Contenu du cadre 4...</p>
                        <a href="#" class="btn btn-primary mt-auto">Action</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>



<script>

    // Sélectionne l'élément avec la classe .navbar puis Ajoute la classe "bg-light" à l'élément
    document.querySelector('.navbar').classList.add('bg-light');

    document.querySelector('.navbar').classList.add('custom-nav');

</script>
@endsection

