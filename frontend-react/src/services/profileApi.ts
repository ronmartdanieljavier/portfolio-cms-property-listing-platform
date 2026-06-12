import api from "../lib/axios";
import type { User } from "../types/auth";

export async function updateProfile(payload: {
  name?: string;
  email?: string;
  password?: string;
  password_confirmation?: string;
}): Promise<{ message: string; user: User }> {
  const { data } = await api.patch<{ message: string; user: User }>(
    "/profile",
    payload,
  );
  return data;
}
