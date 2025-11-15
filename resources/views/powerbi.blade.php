@extends('admin.layouts.admin')

@section('title', 'Informe Power BI')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center">
                <i class="fas fa-chart-bar me-2"></i>
                Informe Financiero - Power BI
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body p-0">
                    <div style="position: relative; width: 100%; padding-bottom: 56.25%; height: 0;">
                        <iframe
                            title="Financial Report"
                            src="https://app.powerbi.com/view?r=eyJrIjoiZGU4NGI0MzQtNDAwZi00NzQ4LWJiOWYtZDE5OTA4NzdjMzkyIiwidCI6IjRiZjM4ZWEyLTgzMmQtNDU1Mi1iNTA4LTQyMTU3MGRhNDNmZiIsImMiOjR9"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
                            allowFullScreen="true">
                        </iframe>
                    </div>
                </div>
                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Este informe se visualiza mejor en pantalla completa. Si ves un error, asegúrate de estar autenticado en Power BI.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection