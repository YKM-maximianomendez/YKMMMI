const SwalCerrarFalla = (orden, callback = null) => Swal.fire({
    icon: 'question',
    title: 'Terminar Falla',
    text: `¿Marcar como terminada la falla ${orden.no_falla}?`,
    showCancelButton: true,
    confirmButtonText: "Confirmar",
    cancelButtonText: "Cancelar",
    showLoaderOnConfirm: true,
    preConfirm: async () => {
        return axios.post(route('mantenimiento.ordenreparacion-falla.cerrar', Object.values(orden)), {
            '_method': 'PUT'
        })
            .then(response => response.data)
            .catch(error => {
                const { errorMessage } = handleErrors(error);
                Swal.showValidationMessage(`Error: ${errorMessage}`);
            })
    },
    allowOutsideClick: () => !Swal.isLoading()
}).then((result) => {
    if(result.isConfirmed){
        Swal.fire({
            icon: "success",
            text: "Falla terminada exitosamente.",
            showConfirmButton: false,
            timer: 1500,
            willClose: callback || function () {}
         }).then()
    }
})

export default SwalCerrarFalla