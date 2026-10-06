// Punto de compatibilidad: el cliente canonico vive en shared/services.
// Se mantiene esta ruta para no romper los imports existentes.
export { httpClient as api } from '../shared/services/httpClient';
