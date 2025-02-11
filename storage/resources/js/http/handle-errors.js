export default (error) => {
    let errors = [];
    let message = null;

    if (error.response) {
        message = error.response.data.message ?? "Ocurrió un error en la respuesta.";

        if (error.response.status === 422 && error.response.data.hasOwnProperty('errors')) {
            errors = error.response.data.errors;
        }
    } else if (error.request) {
        message = "Ocurrió un error en la petición";
    } else {
         message = error.message;
    }

    return {
        validationErrors: errors,
        errorMessage: message
    }
}
