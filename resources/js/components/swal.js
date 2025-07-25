// resources/js/components/swal.js

/**
 * SweetAlert2 - Confirmación de eliminación unificada (compacta)
 */
export function confirmDelete({
    title = 'Confirmar eliminación',
    text = '¿Seguro que deseas eliminar este registro? Esta acción no se puede deshacer.',
    confirmButtonText = 'Eliminar',
    confirmButtonColor = '#dc2626', // rojo
    icon = 'warning'
} = {}) {
    return Swal.fire({
        title: `<span style="color:#dc2626"><i class="fas fa-exclamation-triangle"></i> ${title}</span>`,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#888',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancelar',
        focusCancel: true,
        width: 350, // Compacto
        customClass: {
            popup: 'swal2-compact-modal',
            confirmButton: 'swal2-confirm btn btn-danger',
            cancelButton: 'swal2-cancel btn btn-secondary'
        }
    });
}

/**
 * SweetAlert2 - Mensaje toast de éxito unificado
 */
export function toastSuccess(message = '¡Operación exitosa!') {
    return Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1700,
        timerProgressBar: true,
        width: 320,
        customClass: {
            popup: 'swal2-toast rounded-lg shadow-lg px-6 py-3 bg-primary text-white text-sm',
            title: 'text-white',
            icon: 'text-white'
        }
    });
}

/**
 * SweetAlert2 - Mensaje toast de error unificado
 */
export function toastError(message = 'Ocurrió un error inesperado') {
    return Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: message,
        showConfirmButton: false,
        timer: 2100,
        timerProgressBar: true,
        width: 320,
        customClass: {
            popup: 'swal2-toast rounded-lg shadow-lg px-6 py-3 bg-red-600 text-white text-sm',
            title: 'text-white',
            icon: 'text-white'
        }
    });
}
