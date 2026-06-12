import type { User } from "../types/auth";

function getUser(): User | null {
  try {
    return JSON.parse(localStorage.getItem("user") ?? "null");
  } catch {
    return null;
  }
}

export default function Dashboard() {
  const user = getUser();

  return (
    <div className="flex items-center justify-center py-24">
      <div className="text-center">
        <p className="text-gray-500 text-sm">
          Welcome back,{" "}
          <span className="font-medium text-gray-900">
            {user?.name ?? "user"}
          </span>
          .
        </p>
      </div>
    </div>
  );
}
