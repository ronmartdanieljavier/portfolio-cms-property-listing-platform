import api from "../lib/axios";
import type { User } from "../types/auth";

export async function getUsers(): Promise<User[]> {
  const { data } = await api.get<User[]>("/admin/users");
  return data;
}

export async function toggleUserStatus(
  userId: number,
): Promise<{ message: string; user: User }> {
  const { data } = await api.patch<{ message: string; user: User }>(
    `/admin/users/${userId}/toggle-status`,
  );
  return data;
}

export async function forceLogoutUser(
  userId: number,
): Promise<{ message: string }> {
  const { data } = await api.delete<{ message: string }>(
    `/admin/users/${userId}/force-logout`,
  );
  return data;
}
