import { Navigate } from "react-router-dom";
import type { User } from "../types/auth";

function getUser(): User | null {
  try {
    return JSON.parse(localStorage.getItem("user") ?? "null");
  } catch {
    return null;
  }
}

export default function AdminRoute({
  children,
}: {
  children: React.ReactNode;
}) {
  const user = getUser();
  return user?.role === "admin" ? (
    <>{children}</>
  ) : (
    <Navigate to="/dashboard" replace />
  );
}
