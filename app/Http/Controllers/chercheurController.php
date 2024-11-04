<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Chercheur;
use App\Models\Contenir;
use App\Models\Document;
use App\Models\Revue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;



class chercheurController extends Controller
{

    public function listeArticles(Request $request)
    {
        // Récupérer les articles avec leurs documents associés, paginés par ordre décroissant de leur date de création
        $articles = Article::with('documents')
        ->orderBy('created_at', 'desc')
        ->paginate(5);


        // Récupérer toutes les années pour le filtre
        $annees = Article::selectRaw('YEAR(created_at) as year')
                        ->distinct()
                        ->pluck('year');

        return view('lab.chercheur.index', compact('articles', 'annees'));
    }

    public function rechercheArticle(Request $request)
    {
        $query = $request->input('query');

        // Stocker la requête de recherche dans la session
        $request->session()->put('search_query', $query);

        // Rechercher des articles par titre ou description
        $articles = Article::where('titre', 'LIKE', "%$query%")
                            ->orWhere('description', 'LIKE', "%$query%")
                            ->paginate(5);

        // Récupérer toutes les années pour le filtre
        $annees = Article::selectRaw('YEAR(created_at) as year')
                        ->distinct()
                        ->pluck('year');

        return view('lab.visiteur.articles', compact('articles', 'annees'));
    }

    public function filtreArticle(Request $request)
    {
        $annee = $request->input('annee');

        if ($annee && $annee !== 'Tous') {
            $articles = Article::whereYear('created_at', $annee)->paginate(5);
        } else {
            $articles = Article::paginate(5);
        }

        // Récupérer toutes les années pour le filtre
        $annees = Article::selectRaw('YEAR(created_at) as year')
                        ->distinct()
                        ->pluck('year');

        return view('lab.visiteur.articles', compact('articles', 'annees'));
    }

    public function modifierArticle(Request $request, Article $article){
        return view('lab.chercheur.modifier_article', compact('article'));

    }



    public function telecharger(Request $request, $documentId)
    {
        // Récupérer le document par son ID
        $document = Document::find($documentId);

        // Vérifier si le document existe
        if (!$document) {
            return redirect()->back()->with('error', 'Le document n\'existe pas.');
        }

        // Vérifier si le champ 'lien' n'est pas null
        if (is_null($document->lien)) {
            return redirect()->back()->with('error', 'Le lien du document est vide.');
        }

        // Récupérer l'article associé au document
        $article = $document->article;

        // Vérifier si l'article existe
        if (!$article) {
            return redirect()->back()->with('error', 'L\'article associé n\'existe pas.');
        }

        // Déterminer l'extension du document à télécharger
        $extension = pathinfo($document->lien, PATHINFO_EXTENSION);

        // Construire le nom de fichier basé sur le titre de l'article et l'extension du document
        $safeTitle = preg_replace('/[^A-Za-z0-9\-]/', '_', $article->titre); // Remplace les caractères non alphanumériques par des underscores
        $filename = $safeTitle . '.' . $extension;

        // Chemin du fichier
        $filePath = 'public/' . $document->lien;

        // Vérifier si le fichier existe
        if (!Storage::disk('public')->exists($document->lien)) {
            Log::error("File does not exist at path: $filePath");
            return redirect()->back()->with('error', 'Le fichier n\'existe pas.');
        }

        // Télécharger le fichier avec le nom de fichier spécifié
        return Storage::disk('public')->download($document->lien, $filename);
    }


    // Formulaire pour enregistrer une revue
    public function enregistrerRevueForm()
    {
        $articles = Article::all();
        return view('lab.chercheur.enregistrer_revue', compact('articles'));
    }

    // Enregistrer une nouvelle revue
    public function enregistrerRevue(Request $request)
    {
        $messages = [
            'cod_ISSN.required' => 'Le code ISSN est obligatoire.',
            'cod_ISSN.string' => 'Le code ISSN doit être une chaîne de caractères.',
            'cod_ISSN.max' => 'Le code ISSN ne doit pas dépasser 255 caractères.',
            'cod_DOI.required' => 'Le code DOI est obligatoire.',
            'cod_DOI.string' => 'Le code DOI doit être une chaîne de caractères.',
            'cod_DOI.max' => 'Le code DOI ne doit pas dépasser 255 caractères.',
            'editeur.string' => 'L\'éditeur doit être une chaîne de caractères.',
            'editeur.max' => 'L\'éditeur ne doit pas dépasser 255 caractères.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'indexe.boolean' => 'La valeur de l\'index doit être vraie ou fausse.',
            'organisme_index.string' => 'L\'organisme d\'indexation doit être une chaîne de caractères.',
            'organisme_index.max' => 'L\'organisme d\'indexation ne doit pas dépasser 255 caractères.',
        ];

        $request->validate([
            'cod_ISSN' => 'required|string|max:255',
            'cod_DOI' => 'required|string|max:255',
            'editeur' => 'nullable|string|max:255',
            'titre' => 'nullable|string|max:255',
            'indexe' => 'nullable|boolean',
            'organisme_index' => 'nullable|string|max:255',
        ], $messages);


        $revue = new Revue();
        $revue->cod_ISSN = $request->cod_ISSN;
        $revue->cod_DOI = $request->cod_DOI;
        $revue->editeur = $request->editeur;
        $revue->titre = $request->titre;
        $revue->indexe = $request->indexe;
        $revue->organisme_index = $request->organisme_index;
        $revue->save();

        return redirect()->route('chercheur.listeRevues')->with('success', 'Revue enregistrée avec succès!');
    }

    // Afficher la liste des revues

    public function listeRevues(Request $request)
    {
        // Supprimer la clé de session 'search' pour effacer les recherches précédentes
        Session::forget('search');

        $revues = Revue::orderBy('created_at', 'desc')->paginate(4);
        return view('lab.chercheur.liste_revue', compact('revues'));
    }



    public function modifierRevueForm(Revue $revue)
    {

        return view('lab.chercheur.modifier_revue', compact('revue'));
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
                    ->paginate(10); // Nombre d'éléments par page

        return view('lab.chercheur.liste_revue', compact('revues', 'search'));
    }



    // Modifier une revue
    public function enregistrerModificationRevue(Request $request, Revue $revue)
    {
        $messages = [
            'cod_ISSN.required' => 'Le code ISSN est obligatoire.',
            'cod_ISSN.string' => 'Le code ISSN doit être une chaîne de caractères.',
            'cod_ISSN.max' => 'Le code ISSN ne doit pas dépasser 255 caractères.',
            'cod_DOI.required' => 'Le code DOI est obligatoire.',
            'cod_DOI.string' => 'Le code DOI doit être une chaîne de caractères.',
            'cod_DOI.max' => 'Le code DOI ne doit pas dépasser 255 caractères.',
            'editeur.string' => 'L\'éditeur doit être une chaîne de caractères.',
            'editeur.max' => 'L\'éditeur ne doit pas dépasser 255 caractères.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'indexe.boolean' => 'La valeur de l\'index doit être vraie ou fausse.',
            'organisme_index.string' => 'L\'organisme d\'indexation doit être une chaîne de caractères.',
            'organisme_index.max' => 'L\'organisme d\'indexation ne doit pas dépasser 255 caractères.',
        ];

        $request->validate([
            'cod_ISSN' => 'required|string|max:255',
            'cod_DOI' => 'required|string|max:255',
            'editeur' => 'nullable|string|max:255',
            'titre' => 'nullable|string|max:255',
            'indexe' => 'nullable|boolean',
            'organisme_index' => 'nullable|string|max:255',
        ], $messages);

        $revue->cod_ISSN = $request->cod_ISSN;
        $revue->cod_DOI = $request->cod_DOI;
        $revue->editeur = $request->editeur;
        $revue->titre = $request->titre;
        $revue->indexe = $request->indexe;
        $revue->organisme_index = $request->organisme_index;
        $revue->save();

        return redirect()->route('chercheur.listeRevues')->with('success', 'Revue modifiée avec succès!');
    }

    // Supprimer une revue
    public function supprimerRevue(Revue $revue)
    {
        $revue->delete();
        return redirect()->route('chercheur.listeRevues')->with('success', 'Revue supprimée avec succès!');
    }


    public function inclureArticleDansRevue(){
        $articles = Article::all(); // Récupérez tous les articles pour les options du formulaire
        $revues = Revue::all(); // Récupérez tous les articles pour les options du formulaire

        return view('lab.chercheur.inclure_article_dans_revue', compact('articles', 'revues'));
    }

    // Méthode pour enregistrer l'association article-revue
    public function enregistrerAssociationArticleRevue(Request $request)
    {
        // Définition des messages de validation personnalisés
        $messages = [
            'client_id.required' => 'Le champ "Revue" est requis.',
            'client_id.exists' => 'La revue sélectionnée n\'existe pas dans la base de données.',
            'articles.required' => 'Au moins un article doit être sélectionné.',
            'articles.array' => 'Les articles doivent être sous forme de tableau.',
            'articles.*.exists' => 'Un des articles sélectionnés n\'existe pas dans la base de données.',
            'specific_info.required' => 'Les informations spécifiques pour chaque article sont requises.',
            'specific_info.array' => 'Les informations spécifiques doivent être sous forme de tableau.',
            'specific_info.*.PageDebut.string' => 'Le champ "Page de début" doit être une chaîne de caractères.',
            'specific_info.*.PageFin.string' => 'Le champ "Page de fin" doit être une chaîne de caractères.',
            'specific_info.*.DatePublication.date' => 'Le champ "Date de publication" doit être une date valide.',
            'specific_info.*.Volume.string' => 'Le champ "Volume" doit être une chaîne de caractères.',
            'specific_info.*.Numero.string' => 'Le champ "Numéro" doit être une chaîne de caractères.',
        ];

        // Validation des données avec les messages personnalisés
        $validatedData = $request->validate([
            'client_id' => 'required|exists:revues,id',
            'articles' => 'required|array',
            'articles.*' => 'exists:articles,id',
            'specific_info' => 'required|array',
            'specific_info.*.PageDebut' => 'nullable|string',
            'specific_info.*.PageFin' => 'nullable|string',
            'specific_info.*.DatePublication' => 'nullable|date',
            'specific_info.*.Volume' => 'nullable|string',
            'specific_info.*.Numero' => 'nullable|string',
        ], $messages);

        try {
            // Récupérer la revue
            $revue = Revue::findOrFail($validatedData['client_id']);

            // Enregistrer chaque association article-revue avec les informations spécifiques
            foreach ($validatedData['articles'] as $key => $articleId) {
                $article = Article::findOrFail($articleId);

                // Créer une nouvelle instance d'ArticleRevue
                $articleRevue = new Contenir();
                $articleRevue->num_art = $article->id;
                $articleRevue->num_rev = $revue->id;
                $articleRevue->PageDebut = $validatedData['specific_info'][$key + 1]['PageDebut'] ?? null;
                $articleRevue->PageFin = $validatedData['specific_info'][$key + 1]['PageFin'] ?? null;
                $articleRevue->DatePublication = $validatedData['specific_info'][$key + 1]['DatePublication'] ?? null;
                $articleRevue->Volume = $validatedData['specific_info'][$key + 1]['Volume'] ?? null;
                $articleRevue->Numero = $validatedData['specific_info'][$key + 1]['Numero'] ?? null;
                $articleRevue->save();
            }

            // Redirection avec un message de succès
            return redirect()->route('chercheur.associer-article-revue')->with('success', 'Association article-revue enregistrée avec succès.');

        } catch (\Exception $e) {
            // En cas d'erreur, retourner avec un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'association article-revue : ' . $e->getMessage());
        }
    }


    public function enregistrerModificationArticle(Request $request, Article $article)
    {
        // Valider les données du formulaire sans limiter la taille des fichiers
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf', // Suppression de 'max:5120'
            'image_document' => 'nullable|image|mimes:jpg,png,jpeg', // Suppression de 'max:2048'
        ], [
            'titre.required' => 'Le titre de l\'article est requis.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.required' => 'La description de l\'article est requise.',
            'document.mimes' => 'Le document doit être au format PDF.',
            'image_document.image' => 'L\'image doit être au format JPG, PNG ou JPEG.',
            'image_document.mimes' => 'L\'image doit être au format JPG, PNG ou JPEG.',
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
        return redirect()->route('chercheur.espace')->with('success', 'Article modifié avec succès!');
    }



    public function supprimerArticle($articleId)
    {
        // Supprime l'article spécifié
        $article = Article::findOrFail($articleId);
        $article->delete();

        return redirect()->route('chercheur.espace')->with('success', 'Article supprimé avec succès');
    }

    public function publierArticle(){
        $revues = Revue::all();
        return view('lab.chercheur.publier_article', compact('revues'));

    }


    public function enregistrerPublication(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'required|file|mimes:pdf,docx', // Autoriser les formats PDF et DOCX
            'image_document' => 'required|image|mimes:jpg,png,jpeg', // Autoriser les formats JPG, PNG, JPEG
        ], [
            'titre.required' => 'Le titre de l\'article est requis.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.required' => 'La description de l\'article est requise.',
            'document.required' => 'Le document PDF ou Word est requis.',
            'document.mimes' => 'Le document doit être au format PDF ou DOCX.',
            'image_document.required' => 'L\'image du document est requise.',
            'image_document.image' => 'L\'image doit être au format JPG, PNG ou JPEG.',
            'image_document.mimes' => 'L\'image doit être au format JPG, PNG ou JPEG.',
        ]);

        // Enregistrer les fichiers
        $documentPath = $request->file('document')->store('documents', 'public');
        $imageDocumentPath = $request->file('image_document')->store('images', 'public');

        // Date de création antérieure à 2024 (par exemple 2023)
        $datePublication = Carbon::create(2023, 1, 1); // 1er janvier 2023

        // Créer un nouvel article
        $article = new Article();
        $article->titre = $request->input('titre');
        $article->description = $request->input('description');
        $article->id_ch = Auth::id(); // Associez l'article au chercheur connecté
        // $article->created_at = $datePublication; // Assigner la date de création
        $article->save();

        // Enregistrer les informations sur le document
        $document = new Document();
        $document->num_art = $article->id;
        $document->format = $request->file('document')->extension(); // Extension du fichier
        $document->lien = $documentPath;
        $document->image = $imageDocumentPath;
        $document->save();

        // Associer l'article à la revue sélectionnée, si elle est fournie
        if ($request->filled('revue')) {
            $article->revues()->attach($request->input('revue'));
        }
        // Rediriger avec un message de succès
        return redirect()->route('chercheur.publierArticle')->with('success', 'Article publié avec succès!');
    }


    public function modifierProfil(Request $request, $id) {

        // Valider les données du formulaire
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'current_password' => 'nullable|string|min:8', // Validation facultative pour le mot de passe actuel
            'new_password' => 'nullable|string|min:8|confirmed', // Confirmation du nouveau mot de passe
        ]);

        // Récupérer l'utilisateur connecté
        $chercheur = Auth::user();

        // Mettre à jour les informations de base
        $chercheur->nom = $request->input('nom');
        $chercheur->prenom = $request->input('prenom');
        $chercheur->contact = $request->input('contact');
        $chercheur->email = $request->input('email');

        // Mettre à jour le mot de passe si un nouveau mot de passe est fourni
        if ($request->filled('new_password')) {
            // Vérifier le mot de passe actuel s'il est fourni et correspond
            if ($request->filled('current_password') && Hash::check($request->input('current_password'), $chercheur->password)) {
                // Mettre à jour le mot de passe
                $chercheur->password = Hash::make($request->input('new_password'));
            } else {
                // Retourner une erreur si le mot de passe actuel ne correspond pas
                return redirect()->back()->with('error', 'Le mot de passe actuel est incorrect.');
            }
        }

        // Sauvegarder les modifications
        $chercheur->save();

        // Redirigez l'utilisateur vers une page de confirmation ou une autre destination
        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }



    public function profil(){
        return view('lab.chercheur.profil');
    }


}
