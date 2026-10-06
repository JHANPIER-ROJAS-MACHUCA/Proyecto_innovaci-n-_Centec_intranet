import { useCallback, useEffect, useState } from 'react';
import { httpClient } from '../services/httpClient';

/**
 * Hook para peticiones GET con estados de carga/error.
 * @param {string|null} endpoint - Si es null no dispara la peticion.
 * @param {object} options - { auto: true, deps: [] }
 */
export function useApi(endpoint, { auto = true, deps = [] } = {}) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(!!endpoint && auto);
    const [error, setError] = useState(null);

    const fetchData = useCallback(async () => {
        if (!endpoint) return null;
        setLoading(true);
        setError(null);
        try {
            const res = await httpClient.get(endpoint);
            setData(res.data);
            return res.data;
        } catch (err) {
            setError(err);
            return null;
        } finally {
            setLoading(false);
        }
    }, [endpoint]);

    useEffect(() => {
        if (auto) fetchData();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [fetchData, ...deps]);

    return { data: data ?? [], loading, error, refetch: fetchData };
}
