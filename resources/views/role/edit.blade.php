@extends('layouts.app')
@section('title', 'Editar Role (Regra)')
@section('content')
		<h1>
	        Editar Role (Grupo)
	        <small>{{$role->nome}}</small>
	    </h1>
		

		<form method="POST" enctype="multipart/form-data" action="{{action('RoleController@update',$id)}}">
			@csrf
			<input type="hidden" name="_method" value="PATCH">
			<div class="form-group mb-12">
			    <label for="name">Nome</label>
			    <input type="text" class="form-control" id="name" name="name" value="{{$role->name}}" placeholder="Digite o Nome..." required>
		 	</div>
		 	<div class="form-group mb-12">
			    <label for="label">Rótulo</label>
			    <input type="text" class="form-control" id="label" name="label" value="{{$role->label}}" placeholder="Digite o Rótulo..." required>
		 	</div>

		 	<div>
		 		<hr>
		 	</div>

		 	<button type="submit" class="btn btn-primary">Atualizar</button>
		</form>
@endsection