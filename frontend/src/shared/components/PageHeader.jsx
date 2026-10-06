export default function PageHeader({ title, actionLabel, onAction, actionVariant = 'primary' }) {
    return (
        <div className="d-flex justify-content-between align-items-center mb-4">
            <h2>{title}</h2>
            {actionLabel && (
                <button className={`btn btn-${actionVariant}`} onClick={onAction}>
                    {actionLabel}
                </button>
            )}
        </div>
    );
}
