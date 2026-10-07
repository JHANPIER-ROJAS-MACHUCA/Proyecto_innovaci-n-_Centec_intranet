import { api } from './api.js';

export const campoService = {
  cobrosHoy: (search) => api.get(`/api/campo/cobros-hoy${search ? `?search=${encodeURIComponent(search)}` : ''}`),
  creditToPay: (creditId) => api.get(`/api/campo/credit-to-pay?creditId=${creditId}`),
};

export const dniService = {
  consultar: (number) => api.get(`/api/util/dni?number=${number}`),
};

export const ubigeoService = {
  departamentos: () => api.get('/api/util/departamentos'),
  provincias: (department_id) => api.get(`/api/util/provincias${department_id ? `?department_id=${department_id}` : ''}`),
  distritos: (province_id) => api.get(`/api/util/distritos${province_id ? `?province_id=${province_id}` : ''}`),
};
