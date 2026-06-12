import { NavLink, Outlet } from "react-router-dom";
import { useLogout } from "../hooks/useAuth";
import type { User } from "../types/auth";

function getUser(): User | null {
  try {
    return JSON.parse(localStorage.getItem("user") ?? "null");
  } catch {
    return null;
  }
}

const navLinkClass = ({ isActive }: { isActive: boolean }) =>
  [
    "flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors",
    isActive
      ? "bg-indigo-50 text-indigo-700"
      : "text-gray-700 hover:bg-gray-100",
  ].join(" ");

export default function AuthLayout() {
  const { logout, processing } = useLogout();
  const user = getUser();
  const isAdmin = user?.role === "admin";

  return (
    <div className="flex min-h-screen bg-gray-50">
      {/* Sidebar */}
      <aside className="w-56 shrink-0 border-r border-gray-200 bg-white flex flex-col">
        <div className="px-4 py-5 border-b border-gray-200">
          <p className="text-sm font-semibold text-gray-900 truncate">
            {user?.name ?? "User"}
          </p>
          <p className="text-xs text-gray-500 capitalize mt-0.5">
            {user?.role}
          </p>
        </div>

        <nav className="flex-1 px-3 py-4 space-y-1">
          {isAdmin && (
            <NavLink to="/agent-users" className={navLinkClass}>
              Agent Users
            </NavLink>
          )}
          <NavLink to="/properties" className={navLinkClass}>
            Properties
          </NavLink>
        </nav>

        <div className="px-3 py-4 border-t border-gray-200">
          <button
            onClick={logout}
            disabled={processing}
            className="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            {processing ? "Signing out…" : "Sign out"}
          </button>
        </div>
      </aside>

      {/* Main content */}
      <div className="flex-1 flex flex-col min-w-0">
        <main className="flex-1 p-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
