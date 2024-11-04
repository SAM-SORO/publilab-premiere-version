<?php

namespace App\Http\Controllers;
use Carbon\Carbon; // Importez la classe Carbon pour le formatage de la date
use App\Models\Article;
use App\Models\Chercheur;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VisiteurController extends Controller
{


    public function pageAccueil(){
        return view('lab/visiteur/index');
    }

    public function Articles(){

        // Récupérer les articles paginés avec les informations sur les chercheurs
        $articles = Article::paginate(5);

        $annees = Article::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->orderBy('annee', 'desc')
                ->pluck('annee');

        // Retourner la vue avec les articles paginés et les informations sur les chercheurs
        return view('lab.visiteur.articles', compact('articles','annees'));
    }


    public function telecharger($documentId)
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



    public function connexion(){
        return view('lab.auth.login');
    }

    public function inscription(){
        return view('lab.auth.register');
    }


    public function rechercheArticleParAuteur() {
        return view("lab.visiteur.recherche");
    }
}
