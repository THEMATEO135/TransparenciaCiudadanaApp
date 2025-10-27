<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Reporte #{{ $feedback->reporte->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .star-rating {
            font-size: 2rem;
            cursor: pointer;
        }
        .star-rating i {
            color: #ddd;
            transition: color 0.2s;
        }
        .star-rating i.active {
            color: #FFD700;
        }
        .nps-button {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #dee2e6;
            background: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        .nps-button.selected {
            background: #ff6600;
            color: white;
            border-color: #ff6600;
        }
        .nps-button:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header text-white" style="background: #ff6600;">
                        <h4 class="mb-0"><i class="fas fa-comments"></i> Tu opinión es importante</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Reporte #{{ $feedback->reporte->id }}</strong> - {{ $feedback->reporte->servicio->nombre }}
                            <br>
                            <small>Reportado el {{ $feedback->reporte->created_at->format('d/m/Y') }}</small>
                        </div>

                        <form action="{{ route('feedback.responder', $feedback->token) }}" method="POST">
                            @csrf

                            <!-- Pregunta 1: ¿Se resolvió? -->
                            <div class="mb-4">
                                <h5>1. ¿Se resolvió tu problema?</h5>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="resuelto" id="resuelto_si" value="1" required>
                                    <label class="btn btn-outline-success" for="resuelto_si">
                                        <i class="fas fa-check-circle"></i> Sí, está resuelto
                                    </label>

                                    <input type="radio" class="btn-check" name="resuelto" id="resuelto_no" value="0">
                                    <label class="btn btn-outline-danger" for="resuelto_no">
                                        <i class="fas fa-times-circle"></i> No, persiste el problema
                                    </label>
                                </div>
                            </div>

                            <!-- Pregunta 2: Calificación -->
                            <div class="mb-4">
                                <h5>2. ¿Cómo calificarías el servicio? (1-5 estrellas)</h5>
                                <input type="hidden" name="calificacion" id="calificacion" required>
                                <div class="star-rating text-center">
                                    <i class="fas fa-star" data-rating="1"></i>
                                    <i class="fas fa-star" data-rating="2"></i>
                                    <i class="fas fa-star" data-rating="3"></i>
                                    <i class="fas fa-star" data-rating="4"></i>
                                    <i class="fas fa-star" data-rating="5"></i>
                                </div>
                                <p class="text-center text-muted mt-2" id="rating-text">Selecciona una calificación</p>
                            </div>

                            <!-- Pregunta 3: NPS -->
                            <div class="mb-4">
                                <h5>3. ¿Qué tan probable es que recomiendes este servicio? (0-10)</h5>
                                <input type="hidden" name="nps" id="nps" required>
                                <div class="d-flex justify-content-between flex-wrap gap-2">
                                    @for($i = 0; $i <= 10; $i++)
                                    <button type="button" class="nps-button" data-nps="{{ $i }}">{{ $i }}</button>
                                    @endfor
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">Muy improbable</small>
                                    <small class="text-muted">Muy probable</small>
                                </div>
                            </div>

                            <!-- Comentario -->
                            <div class="mb-4">
                                <h5>4. Comentarios adicionales (opcional)</h5>
                                <textarea name="comentario" class="form-control" rows="4" placeholder="Cuéntanos más sobre tu experiencia..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-lg w-100 text-white" style="background: #ff6600;">
                                <i class="fas fa-paper-plane"></i> Enviar Feedback
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Star Rating
    document.querySelectorAll('.star-rating i').forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            document.getElementById('calificacion').value = rating;

            document.querySelectorAll('.star-rating i').forEach(s => {
                s.classList.remove('active');
                if (s.dataset.rating <= rating) {
                    s.classList.add('active');
                }
            });

            const texts = ['Muy malo', 'Malo', 'Regular', 'Bueno', 'Excelente'];
            document.getElementById('rating-text').textContent = texts[rating - 1];
        });

        star.addEventListener('mouseenter', function() {
            const rating = this.dataset.rating;
            document.querySelectorAll('.star-rating i').forEach(s => {
                if (s.dataset.rating <= rating) {
                    s.style.color = '#FFD700';
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            document.querySelectorAll('.star-rating i').forEach(s => {
                s.style.color = s.classList.contains('active') ? '#FFD700' : '#ddd';
            });
        });
    });

    // NPS Buttons
    document.querySelectorAll('.nps-button').forEach(btn => {
        btn.addEventListener('click', function() {
            const nps = this.dataset.nps;
            document.getElementById('nps').value = nps;

            document.querySelectorAll('.nps-button').forEach(b => {
                b.classList.remove('selected');
            });
            this.classList.add('selected');
        });
    });
    </script>
</body>
</html>
