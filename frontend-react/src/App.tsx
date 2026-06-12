import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom";
import Login from "./pages/Auth/Login";
import Register from "./pages/Auth/Register";
import Dashboard from "./pages/Dashboard";
import AuthLayout from "./layouts/AuthLayout";
import AdminRoute from "./components/AdminRoute";
import AgentUsers from "./pages/AgentUsers/AgentUsers";
import Properties from "./pages/Properties/Properties";

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const token = localStorage.getItem("token");
  return token ? <>{children}</> : <Navigate to="/login" replace />;
}

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Navigate to="/login" replace />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route
          element={
            <ProtectedRoute>
              <AuthLayout />
            </ProtectedRoute>
          }
        >
          <Route path="/dashboard" element={<Dashboard />} />
          <Route
            path="/agent-users"
            element={
              <AdminRoute>
                <AgentUsers />
              </AdminRoute>
            }
          />
          <Route path="/properties" element={<Properties />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}
