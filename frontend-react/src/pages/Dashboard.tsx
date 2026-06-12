import { useLogout } from "../hooks/useAuth";
import type { User } from "../types/auth";

function getUser(): User | null {
  try {
    return JSON.parse(localStorage.getItem("user") ?? "null");
  } catch {
    return null;
  }
}

export default function Dashboard() {
  const { logout, processing } = useLogout();
  const user = getUser();

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h1 className="text-lg font-semibold text-gray-900">Dashboard</h1>

        <div className="flex items-center gap-4">
          {user && (
            <div className="text-right hidden sm:block">
              <p className="text-sm font-medium text-gray-900">{user.name}</p>
              <p className="text-xs text-gray-500 capitalize">{user.role}</p>
            </div>
          )}

          <button
            onClick={logout}
            disabled={processing}
            className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            {processing ? "Signing out…" : "Sign out"}
          </button>
        </div>
      </header>

      <main className="flex items-center justify-center py-24">
        <div className="text-center">
          <p className="text-gray-500 text-sm">
            Welcome back,{" "}
            <span className="font-medium text-gray-900">
              {user?.name ?? "user"}
            </span>
            .
          </p>
        </div>
      </main>
    </div>
  );
}
