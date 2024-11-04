<!doctype html>
<html lang="en">
<head>
    <title>Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
    <style>
        .custom-link {
            text-decoration: none;
            color: inherit; /* Keeps the original color */
        }

        .custom-link:hover {
            text-decoration: underline;
            color: inherit; /* Ensures color does not change on hover */
        }
    </style>
</head>
<body class="bg-light">

    <div class="wrapper d-flex align-items-stretch">
        <nav id="sidebar">
            <div class="custom-menu">
                <button type="button" id="sidebarCollapse" class="btn btn-primary">
                    <i class="fa fa-bars"></i>
                    <span class="sr-only">Toggle Menu</span>
                </button>
            </div>
            <div class="p-4">
                <h1><a href="{{ route('visiteur.article') }}" class="logo">Publi-lab</a></h1>
                <div class="mt-4">
                    <div>
                        <ul class="list-unstyled components mb-5">
                            <li class="mb-3"><a href="{{ route('admin.liste-articles') }}"><span class="fas fa-book mr-3"></span>Liste des Articles</a></li>
                            <li class="mb-3"><a href="{{ route('admin.publier-article') }}"><span class="fas fa-feather-alt mr-3"></span>Publier un Article</a></li>
                            <li class="mb-3"><a href="{{ route('admin.enregistrer-revue-formulaire') }}"><span class="fas fa-journal-whills mr-3"></span>Enregistrer une Revue</a></li>
                            <li class="mb-3"><a href="{{ route('admin.liste-revues') }}"><span class="fas fa-list mr-3"></span>Liste des Revues</a></li>
                            <li class="mb-3"><a href="{{ route('admin.associer-article-revue') }}"><span class="fas fa-link mr-3"></span>Associer Article-Revue</a></li>
                            <li class="mb-3"><a href="{{ route('home') }}"><span class="fa-solid fa-house mr-3"></span>Page de Visite</a></li>
                            <li class="mb-3"><a href="{{ route('home') }}"><span class="fa-solid fa-house mr-3"></span>Page de Visite</a></li>
                            <li class="mb-3"><a href="{{ route('home') }}"><span class="fa-solid fa-house mr-3"></span>Page de Visite</a></li>
                            <li class="mb-3"><a href="{{ route('home') }}"><span class="fa-solid fa-house mr-3"></span>Page de Visite</a></li>
                            <li class="mb-3"><a href="{{ route('home') }}"><span class="fa-solid fa-house mr-3"></span>Page de Visite</a></li>

                            <li class="d-flex flex-column justify-content-center justify-center">
                                <a href="{{ route('admin.profil') }}" class="ml-4">
                                    <img src="{{ asset('img/WhatsApp Image 2024-02-26 à 19.58.41_cd0f47c4.jpg') }}" alt="Nom de l'utilisateur" class="rounded-circle img-fluid" width="35" height="50px">
                                </a>
                                <p>{{ Auth::guard('admin')->user()->nom }} - Admin</p>
                            </li>
                            <li class="mb-3 mt-4"><a href="{{ route('chercheur.espace') }}"><span class="fas fa-user-graduate mr-3"></span>Chercheur</a></li> {{-- Nouvelle ligne pour Chercheur --}}
                        </ul>
                    </div>
                    <div>
                        <ul class="footer-sidebar d-flex flex-column">
                            <li>
                                <a href="#">
                                    <span class="fas fa-cog mt-4"></span>
                                    <span class="parameter-label ml-2">Paramètre</span>
                                </a>
                            </li>
                            <li class="">
                                <a href="{{ route('logout') }}">
                                    <span class="fas fa-sign-out-alt"></span>
                                    <span class="logout-label ml-2">Se Déconnecter</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div id="content">
            @yield('content')
        </div>

    </div>

    <script src="{{ asset('assets/bootstrap/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
