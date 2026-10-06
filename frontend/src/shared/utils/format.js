export const formatMoney = (value, currency = 'PEN') => {
    const n = Number(value ?? 0);
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency }).format(n);
};

export const formatDate = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('es-PE');
};

export const formatDateTime = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleString('es-PE');
};
