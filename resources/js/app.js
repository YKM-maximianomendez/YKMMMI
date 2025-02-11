import './bootstrap';
import Alpine from 'alpinejs';
import IMask from 'imask';
import handleErrors from './http/handle-errors';
import SwalCerrarFalla from './mantenimiento/ordenesreparacion-falla/swal-cerrarfalla';

window.IMask = IMask;
window.handleErrors = handleErrors;
window.swalCerrarFalla = SwalCerrarFalla;

window.Alpine = Alpine;
Alpine.start();