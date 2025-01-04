<!-- Modal -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que quieres eliminar este usuario?
            </div>
            <div class="modal-footer">
                <form method="POST" action="eliminar.php"> <!-- El formulario ahora apunta a eliminar.php -->
                    <input type="hidden" name="id_usuario" id="usuarioId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para cargar el ID del usuario en el modal
    const modalEliminar = document.getElementById('modalEliminar');
    modalEliminar.addEventListener('show.bs.modal', function (event) {
        // Obtén el ID del usuario desde el enlace
        const button = event.relatedTarget; // El botón que disparó el modal
        const usuarioId = button.getAttribute('data-id'); // Obtener el ID del usuario
        // Asigna el ID al campo oculto del formulario
        const inputId = modalEliminar.querySelector('#usuarioId');
        inputId.value = usuarioId;
    });
</script>
