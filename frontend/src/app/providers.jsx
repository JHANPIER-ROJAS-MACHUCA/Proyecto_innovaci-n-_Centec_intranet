import { AuthProvider } from '../modules/auth/AuthContext';

export default function Providers({ children }) {
    return <AuthProvider>{children}</AuthProvider>;
}
