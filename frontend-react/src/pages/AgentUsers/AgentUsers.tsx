import { useEffect, useState } from "react";
import {
  getUsers,
  toggleUserStatus,
  forceLogoutUser,
} from "../../services/usersApi";
import type { User } from "../../types/auth";

export default function AgentUsers() {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string>("");
  const [processing, setProcessing] = useState<number | null>(null);
  const [toast, setToast] = useState<string>("");

  useEffect(() => {
    getUsers()
      .then(setUsers)
      .catch(() => setError("Failed to load users."))
      .finally(() => setLoading(false));
  }, []);

  function showToast(msg: string) {
    setToast(msg);
    setTimeout(() => setToast(""), 3000);
  }

  async function handleToggleStatus(user: User) {
    setProcessing(user.id);
    try {
      const res = await toggleUserStatus(user.id);
      setUsers((prev) => prev.map((u) => (u.id === user.id ? res.user : u)));
      showToast(res.message);
    } catch {
      showToast("Failed to update status.");
    } finally {
      setProcessing(null);
    }
  }

  async function handleForceLogout(user: User) {
    if (
      !confirm(
        `Force logout ${user.name}? This will revoke all their sessions.`,
      )
    )
      return;
    setProcessing(user.id);
    try {
      const res = await forceLogoutUser(user.id);
      showToast(res.message);
    } catch {
      showToast("Failed to force logout.");
    } finally {
      setProcessing(null);
    }
  }

  return (
    <div>
      <div className="mb-6">
        <h1 className="text-xl font-semibold text-gray-900">Agent Users</h1>
        <p className="text-sm text-gray-500 mt-1">Manage agent accounts</p>
      </div>

      {toast && (
        <div className="mb-4 rounded-lg bg-indigo-50 border border-indigo-200 px-4 py-2 text-sm text-indigo-700">
          {toast}
        </div>
      )}

      {loading ? (
        <p className="text-sm text-gray-500">Loading…</p>
      ) : error ? (
        <p className="text-sm text-red-600">{error}</p>
      ) : users.length === 0 ? (
        <p className="text-sm text-gray-500">No users found.</p>
      ) : (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white">
          <table className="min-w-full divide-y divide-gray-200 text-sm">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-4 py-3 text-left font-medium text-gray-500">
                  Name
                </th>
                <th className="px-4 py-3 text-left font-medium text-gray-500">
                  Email
                </th>
                <th className="px-4 py-3 text-left font-medium text-gray-500">
                  Role
                </th>
                <th className="px-4 py-3 text-left font-medium text-gray-500">
                  Status
                </th>
                <th className="px-4 py-3 text-right font-medium text-gray-500">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {users.map((user) => (
                <tr key={user.id}>
                  <td className="px-4 py-3 font-medium text-gray-900">
                    {user.name}
                  </td>
                  <td className="px-4 py-3 text-gray-600">{user.email}</td>
                  <td className="px-4 py-3 capitalize text-gray-600">
                    {user.role}
                  </td>
                  <td className="px-4 py-3">
                    <span
                      className={[
                        "inline-flex rounded-full px-2 py-0.5 text-xs font-medium",
                        user.is_active
                          ? "bg-green-50 text-green-700"
                          : "bg-red-50 text-red-700",
                      ].join(" ")}
                    >
                      {user.is_active ? "Active" : "Inactive"}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-right space-x-2">
                    <button
                      disabled={processing === user.id}
                      onClick={() => handleToggleStatus(user)}
                      className="rounded border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 transition"
                    >
                      {user.is_active ? "Deactivate" : "Activate"}
                    </button>
                    <button
                      disabled={processing === user.id}
                      onClick={() => handleForceLogout(user)}
                      className="rounded border border-red-200 bg-white px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50 disabled:opacity-50 transition"
                    >
                      Force logout
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
