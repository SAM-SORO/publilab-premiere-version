<?php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Chercheur;
use App\Models\Contenir;
use App\Models\Document;
use App\Models\Revue;
use App\Models\Visiteur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{

    public function index()
    {
        $nombreChercheurs = Chercheur::count();
        $nombreVisiteurs = Visiteur::count();
        $nombreArticles = Article::count();
        $nombreRevues = Revue::count();

        return view('lab.admin.index', compact('nombreChercheurs', 'nombreVisiteurs', 'nombreArticles', 'nombreRevues'));
    }


    public function listeAricle(Request $request)
    {
        // Récupérer les articles avec leurs documents associés, paginés par ordre décroissant de leur date de création
        $articles = Article::with('documents')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Récupérer toutes les années pour le filtre
        $annees = Article::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year');

        return view('lab.admin.index', compact('articles', 'annees'));
    }

    public function modifierArticle(Request $request, Article $article)
    {
        return view('admin.articles.modifier', compact('article'));
    }

    public function enregistrerModificationArticle(Request $request, Article $article)
    {
        // Valider les données du formulaire sans limiter la taille des fichiers
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf', // Suppression de 'max:5120'
            'image_document' => 'nullable|image|mimes:jpg,png,jpeg', // Suppression de 'max:2048'
        ]);

        // Mettre à jour les champs de l'article
        $article->titre = $request->input('titre');
        $article->description = $request->input('description');
        $article->save();

        // Vérifier si un document existe déjà, sinon en créer un nouveau
        $document = $article->document ?: new Document();
        $document->num_art = $article->id;

        // Mettre à jour les fichiers s'ils sont fournis
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
            $document->lien = $documentPath;
        }

        if ($request->hasFile('image_document')) {
            $imageDocumentPath = $request->file('image_document')->store('images', 'public');
            $document->image = $imageDocumentPath;
        }

        // Sauvegarder les modifications du document seulement s'il a été créé ou modifié
        if ($document->isDirty() || !$document->exists) {
            $document->save();
        }

        // Rediriger avec un message de succès
        return redirect()->route('admin.articles')->with('success', 'Article modifié avec succès!');
    }

    public function supprimerArticle(Article $article)
    {
        // Supprime l'article spécifié
        $article->delete();

        return redirect()->route('admin.articles')->with('success', 'Article supprimé avec succès');
    }

    public function listeRevues(Request $request)
    {
        // Supprimer la clé de session 'search' pour effacer les recherches précédentes
        Session::forget('search');

        $revues = Revue::orderBy('created_at', 'desc')->paginate(4);
        return view('admin.revues.index', compact('revues'));
    }

    public function enregistrerRevueForm()
    {
        $articles = Article::all();
        return view('admin.revues.enregistrer', compact('articles'));
    }

    public function enregistrerRevue(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'cod_ISSN' => 'required|string|max:255',
            'cod_DOI' => 'required|string|max:255',
            'editeur' => 'nullable|string|max:255',
            'titre' => 'nullable|string|max:255',
            'indexe' => 'nullable|boolean',
            'organisme_index' => 'nullable|string|max:255',
        ]);

        $revue = new Revue();
        $revue->cod_ISSN = $request->cod_ISSN;
        $revue->cod_DOI = $request->cod_DOI;
        $revue->editeur = $request->editeur;
        $revue->titre = $request->titre;
        $revue->indexe = $request->indexe;
        $revue->organisme_index = $request->organisme_index;
        $revue->save();

        return redirect()->route('admin.revues')->with('success', 'Revue enregistrée avec succès!');
    }

    public function modifierRevueForm(Revue $revue)
    {
        return view('admin.revues.modifier', compact('revue'));
    }

    public function enregistrerModificationRevue(Request $request, Revue $revue)
    {
        // Valider les données du formulaire
        $request->validate([
            'cod_ISSN' => 'required|string|max:255',
            'cod_DOI' => 'required|string|max:255',
            'editeur' => 'nullable|string|max:255',
            'titre' => 'nullable|string|max:255',
            'indexe' => 'nullable|boolean',
            'organisme_index' => 'nullable|string|max:255',
        ]);

        $revue->cod_ISSN = $request->cod_ISSN;
        $revue->cod_DOI = $request->cod_DOI;
        $revue->editeur = $request->editeur;
        $revue->titre = $request->titre;
        $revue->indexe = $request->indexe;
        $revue->organisme_index = $request->organisme_index;
        $revue->save();

        return redirect()->route('admin.revues')->with('success', 'Revue modifiée avec succès!');
    }

    public function supprimerRevue(Revue $revue)
    {
        $revue->delete();
        return redirect()->route('admin.revues')->with('success', 'Revue supprimée avec succès!');
    }

    public function inclureArticleDansRevue()
    {
        $articles = Article::all();
        $revues = Revue::all();

        return view('admin.revues.inclure-article', compact('articles', 'revues'));
    }

    public function enregistrerAssociationArticleRevue(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'revue_id' => 'required|exists:revues,id',
            'articles' => 'required|array',
            'articles.*' => 'exists:articles,id',
            'specific_info' => 'required|array',
            'specific_info.*.page_debut' => 'nullable|string',
            'specific_info.*.page_fin' => 'nullable|string',
            'specific_info.*.date_publication' => 'nullable|date',
            'specific_info.*.volume' => 'nullable|string',
            'specific_info.*.numero' => 'nullable|string',
        ]);

        try {
            // Récupérer la revue
            $revue = Revue::findOrFail($request->revue_id);

            // Enregistrer chaque association article-revue avec les informations spécifiques
            foreach ($request->articles as $key => $articleId) {
                $article = Article::findOrFail($articleId);

                // Créer une nouvelle instance de Contenir
                $contenir = new Contenir();
                $contenir->num_art = $article->id;
                $contenir->num_rev = $revue->id;
                $contenir->PageDebut = $request->specific_info[$key]['page_debut'] ?? null;
                $contenir->PageFin = $request->specific_info[$key]['page_fin'] ?? null;
                $contenir->DatePublication = $request->specific_info[$key]['date_publication'] ?? null;
                $contenir->Volume = $request->specific_info[$key]['volume'] ?? null;
                $contenir->Numero = $request->specific_info[$key]['numero'] ?? null;
                $contenir->save();
            }

            // Redirection avec un message de succès
            return redirect()->route('admin.revues')->with('success', 'Association article-revue enregistrée avec succès.');

        } catch (\Exception $e) {
            // En cas d'erreur, retourner avec un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'association article-revue : ' . $e->getMessage());
        }
    }

    public function rechercherRevue(Request $request)
    {
        $search = $request->input('search');

        // Stocker la requête de recherche dans la session
        $request->session()->put('search', $search);

        // Requête pour rechercher les revues
        $revues = Revue::where('cod_ISSN', 'like', "%$search%")
            ->orWhere('cod_DOI', 'like', "%$search%")
            ->orWhere('editeur', 'like', "%$search%")
            ->orWhere('titre', 'like', "%$search%")
            ->orWhere('organisme_index', 'like', "%$search%")
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        // Retourner la vue avec les résultats de la recherche
        return view('admin.revues.index', compact('revues'));
    }

    public function listeChercheurs(Request $request)
    {
        $chercheurs = Chercheur::orderBy('created_at', 'desc')->paginate(5);
        return view('admin.chercheurs.index', compact('chercheurs'));
    }

    public function enregistrerChercheurForm()
    {
        return view('admin.chercheurs.enregistrer');
    }

    public function enregistrerChercheur(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:chercheurs,email',
            'password' => 'required|string|min:8',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        // Créer un nouvel objet Chercheur
        $chercheur = new Chercheur();
        $chercheur->nom = $request->nom;
        $chercheur->prenom = $request->prenom;
        $chercheur->email = $request->email;
        $chercheur->password = Hash::make($request->password);
        $chercheur->telephone = $request->telephone;
        $chercheur->adresse = $request->adresse;
        $chercheur->save();

        // Rediriger avec un message de succès
        return redirect()->route('admin.chercheurs')->with('success', 'Chercheur enregistré avec succès!');
    }

    public function modifierChercheurForm(Chercheur $chercheur)
    {
        return view('admin.chercheurs.modifier', compact('chercheur'));
    }

    public function enregistrerModificationChercheur(Request $request, Chercheur $chercheur)
    {
        // Valider les données du formulaire
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:chercheurs,email,' . $chercheur->id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        // Mettre à jour les informations du chercheur
        $chercheur->nom = $request->nom;
        $chercheur->prenom = $request->prenom;
        $chercheur->email = $request->email;
        $chercheur->telephone = $request->telephone;
        $chercheur->adresse = $request->adresse;
        $chercheur->save();

        // Rediriger avec un message de succès
        return redirect()->route('admin.chercheurs')->with('success', 'Chercheur modifié avec succès!');
    }

    public function supprimerChercheur(Chercheur $chercheur)
    {
        $chercheur->delete();
        return redirect()->route('admin.chercheurs')->with('success', 'Chercheur supprimé avec succès!');
    }

    public function profil(){
        return view('lab.admin.profil');
    }


}
