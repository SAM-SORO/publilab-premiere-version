<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\chercheurController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\mesTest;
use App\Http\Controllers\VisiteurController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\RoutePath;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



// routes/web.php

//Route::get('/',

// Connexion
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/register', [LoginController::class, 'register'])->name('register');

Route::post('/register_submit', [LoginController::class, 'register_submit'])->name('submitRegister');

Route::post('/login_submit', [LoginController::class, "login_submit"])->name('submitLogin');
// Route de déconnexion
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Visiteur
Route::get('/', [VisiteurController::class, 'pageAccueil'])->name('home');
Route::get('/articles', [VisiteurController::class, 'Articles'])->name('visiteur.article');
Route::get('/filtre-article', [ChercheurController::class, 'filtreArticle'])->name('filtre.article');
Route::get('/recherche-article', [ChercheurController::class, 'rechercheArticle'])->name('recherche.article');

// Chercheur
Route::middleware(['auth:visiteur'])->group(function(){
    Route::get('/user-telecharger/{document}', [chercheurController::class, 'telecharger'])->name('visiteur.telecharger-article');
});

// Chercheur
Route::middleware(['auth:chercheur'])->group(function(){
    Route::get('/espace-chercheur', [ChercheurController::class, 'listeArticles'])->name('chercheur.espace');

    Route::post('/modifier-article/{article}', [ChercheurController::class, 'modifierArticle'])->name('chercheur.modifier-article');

    Route::post('/enregistrerModification-article/{article}', [ChercheurController::class, 'enregistrerModificationArticle'])->name('chercheur.enregistrer-modification-article');

    Route::post('/chercheur-supprimer-article/{article}', [ChercheurController::class, 'supprimerArticle'])->name('chercheur.supprimer-article');

    Route::post('/publier-article', [ChercheurController::class, 'enregistrerPublication'])->name('chercheur.enregistrer-publication');

    Route::get('/publier-article', [ChercheurController::class, 'publierArticle'])->name('chercheur.publierArticle');

    Route::get('/profil', [ChercheurController::class, 'profil'])->name('chercheur.profil');

    Route::post('/modifier-profil/{id}', [ChercheurController::class, 'modifierProfil'])->name('chercheur.modifier-profil');

    // Routes pour les revues
    Route::get('/enregistrer-revue', [ChercheurController::class, 'enregistrerRevueForm'])->name('chercheur.enregistrerRevueFormulaire');

    Route::post('/enregistrer-revue', [ChercheurController::class, 'enregistrerRevue'])->name('chercheur.enregistrer-revue');

    Route::post('/form-modifier-revue/{revue}', [ChercheurController::class, 'modifierRevueForm'])->name('chercheur.modifier-revue');

    Route::post('/modifier-revue/{revue}', [ChercheurController::class, 'enregistrerModificationRevue'])->name('chercheur.enregistrer-modification-revue');

    Route::post('/supprimer-revue/{revue}', [ChercheurController::class, 'supprimerRevue'])->name('chercheur.supprimer-revue');

    Route::get('/associer-article-revue', [ChercheurController::class, 'inclureArticleDansRevue'])->name('chercheur.associer-article-revue');

    Route::post('/enregistrer-association-article-revue', [ChercheurController::class, 'enregistrerAssociationArticleRevue'] )->name('chercheur.enregistrer-association-article-revue');


    Route::get('/liste-revues', [ChercheurController::class, 'listeRevues'])->name('chercheur.listeRevues');

    // Route pour la recherche de revues
    Route::get('/rechercher-revue', [ChercheurController::class, 'rechercherRevue'])->name('chercheur.rechercherRevue');

    Route::get('/chercheur-telecharger/{document}', [ChercheurController::class, 'telecharger'])->name('chercheur.telecharger-article');

});


// Admin
// Admin
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/espace-admin', [AdminController::class, 'index'])->name('admin.espace');

    // Routes pour les chercheurs
    Route::get('/chercheurs', [AdminController::class, 'listeChercheurs'])->name('admin.liste-chercheurs');
    Route::get('/enregistrer-chercheur', [AdminController::class, 'enregistrerChercheurForm'])->name('admin.enregistrer-chercheur');
    Route::post('/enregistrer-chercheur', [AdminController::class, 'enregistrerChercheur'])->name('admin.enregistrer-chercheur-post');
    Route::get('/modifier-chercheur/{chercheur}', [AdminController::class, 'modifierChercheurForm'])->name('admin.modifier-chercheur');
    Route::post('/modifier-chercheur/{chercheur}', [AdminController::class, 'enregistrerModificationChercheur'])->name('admin.enregistrer-modification-chercheur');
    Route::get('/supprimer-chercheur/{chercheur}', [AdminController::class, 'supprimerChercheur'])->name('admin.supprimer-chercheur');

    // Routes pour les articles
    Route::get('/liste-articles', [AdminController::class, 'listeArticles'])->name('admin.liste-articles');
    Route::get('/modifier-article/{article}', [AdminController::class, 'modifierArticle'])->name('admin.modifier-article');
    Route::post('/modifier-article/{article}', [AdminController::class, 'enregistrerModificationArticle'])->name('admin.enregistrer-modification-article');
    Route::post('/supprimer-article/{article}', [AdminController::class, 'supprimerArticle'])->name('admin.supprimer-article');
    Route::post('/enregistrer-publication', [AdminController::class, 'enregistrerPublication'])->name('admin.enregistrer-publication');
    Route::get('/publier-article', [AdminController::class, 'publierArticle'])->name('admin.publier-article');

    // Routes pour le profil du chercheur
    Route::get('/profil', [AdminController::class, 'profil'])->name('admin.profil');
    Route::post('/modifier-profil/{id}', [AdminController::class, 'modifierProfil'])->name('admin.modifier-profil');

    // Routes pour les revues
    Route::get('/enregistrer-revue', [AdminController::class, 'enregistrerRevueForm'])->name('admin.enregistrer-revue-formulaire');
    Route::post('/enregistrer-revue', [AdminController::class, 'enregistrerRevue'])->name('admin.enregistrer-revue');
    Route::get('/modifier-revue/{revue}', [AdminController::class, 'modifierRevueForm'])->name('admin.modifier-revue');
    Route::post('/modifier-revue/{revue}', [AdminController::class, 'enregistrerModificationRevue'])->name('admin.enregistrer-modification-revue');
    Route::post('/supprimer-revue/{revue}', [AdminController::class, 'supprimerRevue'])->name('admin.supprimer-revue');
    Route::get('/liste-revues', [AdminController::class, 'listeRevues'])->name('admin.liste-revues');
    Route::get('/rechercher-revue', [AdminController::class, 'rechercherRevue'])->name('admin.rechercher-revue');
    Route::get('/associer-article-revue', [AdminController::class, 'inclureArticleDansRevue'])->name('admin.associer-article-revue');
    Route::post('/enregistrer-association-article-revue', [AdminController::class, 'enregistrerAssociationArticleRevue'])->name('admin.enregistrer-association-article-revue');

    // Route pour télécharger un article par le chercheur
    Route::get('/telecharger-article/{document}', [AdminController::class, 'telecharger'])->name('admin.telecharger-article');
});



    // Route:st('/exist_email', [LoginController::class, 'existEmail'])->name('app_exist_email');});


// Route::middleware(['auth', 'role:visiteur'])->group(function () {
//     Route::match(['get', 'post'], '/', [VisiteurController::class, 'pageAccueil'])->name('home');
//
// });




/*

Route::get('/admin/profil',[AdminController::class, 'profilAdmin'])->name('admin.profil');

*/

