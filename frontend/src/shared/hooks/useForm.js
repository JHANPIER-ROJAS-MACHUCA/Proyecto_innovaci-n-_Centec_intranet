import { useState } from 'react';

/** Manejo generico de formularios controlados. */
export function useForm(initialValues = {}) {
    const [values, setValues] = useState(initialValues);
    const [showForm, setShowForm] = useState(false);

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setValues((prev) => ({ ...prev, [name]: type === 'checkbox' ? checked : value }));
    };

    const setField = (name, value) => setValues((prev) => ({ ...prev, [name]: value }));

    const reset = (next = initialValues) => {
        setValues(next);
        setShowForm(false);
    };

    return { values, setValues, handleChange, setField, reset, showForm, setShowForm };
}
