export const isRequired = (value) => value !== undefined && value !== null && String(value).trim() !== '';

export const isEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value ?? ''));

export const isDni = (value) => /^\d{8}$/.test(String(value ?? ''));

export const isRuc = (value) => /^\d{11}$/.test(String(value ?? ''));
