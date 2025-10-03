
@extends('layout')
@section('content')
<h2>Consulta de Reportes</h2>
<form method="GET" action="/consulta">
    <label>Cédula</label>
    <input type="text" name="cedula">
    <button type="submit">Consultar</button>
</form>
@if(isset($reportes))
    <ul>
    @foreach($reportes as $reporte)
        <li>
            {{ $reporte->descripcion }} - Estado: {{ $reporte->estado }}
            @if($reporte->ciudad)
                - Ciudad: {{ $reporte->ciudad->nombre }}
            @endif
            @if($reporte->proveedor)
                - Proveedor: {{ $reporte->proveedor->nombre }}
            @endif
        </li>
    @endforeach
    </ul>
@endif
@endsection
