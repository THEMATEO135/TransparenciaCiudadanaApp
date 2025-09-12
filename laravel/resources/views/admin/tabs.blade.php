
@extends('layout')
@section('content')
<h2>Reportes Filtrados</h2>
<form method="GET" action="/admin/reportes">
    <label>Cédula</label>
    <input type="text" name="cedula">
    <label>Servicio</label>
    <input type="text" name="servicio_id">
    <label>Estado</label>
    <input type="text" name="estado">
    <button type="submit">Filtrar</button>
</form>
<ul>
@foreach($reportes as $reporte)
    <li>{{ $reporte->descripcion }} - Estado: {{ $reporte->estado }}</li>
@endforeach
</ul>
@endsection
