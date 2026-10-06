export default function DataTable({ columns, rows, rowKey = 'id', emptyText = 'Sin registros.' }) {
    return (
        <div className="table-responsive">
            <table className="table table-striped">
                <thead>
                    <tr>
                        {columns.map((col) => (
                            <th key={col.key}>{col.label}</th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {rows.length === 0 && (
                        <tr>
                            <td colSpan={columns.length} className="text-center text-muted">
                                {emptyText}
                            </td>
                        </tr>
                    )}
                    {rows.map((row) => (
                        <tr key={row[rowKey] ?? JSON.stringify(row)}>
                            {columns.map((col) => (
                                <td key={col.key}>{col.render ? col.render(row) : row[col.key]}</td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
