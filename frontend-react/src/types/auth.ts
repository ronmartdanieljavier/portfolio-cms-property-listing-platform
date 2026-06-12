export type UserRole = "admin" | "agent";

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}

export interface LoginForm {
  email: string;
  password: string;
}

export interface RegisterForm {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface ValidationErrors {
  [key: string]: string[];
}

export interface ApiError {
  message: string;
  errors?: ValidationErrors;
}
