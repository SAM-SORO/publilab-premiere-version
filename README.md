# moi
il s'agit de l'ancienne version de publa de l'UPRO TS INFO 2 ici on a pas fait l'hebergment on peut dire qu'on s'est entainer a faire des choses pas possibles

#

Personnalisation des Liens de Pagination
Si vous souhaitez personnaliser davantage les liens de pagination, vous pouvez publier les vues de pagination et les modifier :

Publiez les vues de pagination :

bash
Copier le code
php artisan vendor:publish --tag=laravel-pagination
Modifiez la vue resources/views/vendor/pagination/bootstrap-4.blade.php selon vos besoins.

<!-- Pagination Links -->
<div class="d-flex justify-content-center">
    {{ $clients->links('vendor.pagination.bootstrap-4') }}
</div>
