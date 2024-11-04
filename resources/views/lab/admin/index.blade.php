@extends('baseAdmin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-primary ml-5 mt-5">DASHBORD</h1>

    <div class="container-fluid" style="margin-top: 8%">
        <div class="row flex-column p-4">
            <div class="col-12 mb-4">
                <div class="row">
                    <div class="col-6">
                        <div class="shadow p-5 rounded bg-white">
                            <h5 class="text-danger">Nombre de chercheurs</h5>
                            <h2 class="text-center">{{ $nombreChercheurs }}</h2>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="shadow p-5 rounded bg-white">
                            <h5 class="text-danger">Nombre de visiteurs</h5>
                            <h2 class="text-center">{{ $nombreVisiteurs }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row flex-column p-4">
            <div class="col-12 mt-4">
                <div class="row">
                    <div class="col-6">
                        <div class="shadow p-5 rounded bg-white">
                            <h5 class="text-danger">Nombre d'articles</h5>
                            <h2 class="text-center">{{ $nombreArticles }}</h2>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="shadow p-5 rounded bg-white">
                            <h5 class="text-danger">Nombre de revues</h5>
                            <h2 class="text-center">{{ $nombreRevues }}</h2>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
