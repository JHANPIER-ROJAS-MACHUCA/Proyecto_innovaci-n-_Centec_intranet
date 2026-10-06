export default function Loading({ text = 'Cargando...' }) {
    return (
        <div className="text-center mt-5" role="status" aria-label={text}>
            <div className="spinner-border" />
            <div className="mt-2 text-muted">{text}</div>
        </div>
    );
}
