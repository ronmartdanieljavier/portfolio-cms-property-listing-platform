import { useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import api from "../lib/axios";
import type {
  AuthResponse,
  ApiError,
  LoginForm,
  RegisterForm,
  ValidationErrors,
} from "../types/auth";

export function useLogin() {
  const navigate = useNavigate();
  const [errors, setErrors] = useState<ValidationErrors>({});
  const [generalError, setGeneralError] = useState<string>("");
  const [processing, setProcessing] = useState(false);

  async function submit(form: LoginForm) {
    setProcessing(true);
    setErrors({});
    setGeneralError("");

    try {
      const { data } = await api.post<AuthResponse>("/auth/login", form);
      localStorage.setItem("token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
      navigate("/dashboard");
    } catch (err) {
      if (axios.isAxiosError(err)) {
        const payload = err.response?.data as ApiError | undefined;
        if (err.response?.status === 422 && payload?.errors) {
          setErrors(payload.errors);
        } else {
          setGeneralError(
            payload?.message ?? "Login failed. Please try again.",
          );
        }
      }
    } finally {
      setProcessing(false);
    }
  }

  return { submit, errors, generalError, processing };
}

export function useRegister() {
  const navigate = useNavigate();
  const [errors, setErrors] = useState<ValidationErrors>({});
  const [generalError, setGeneralError] = useState<string>("");
  const [processing, setProcessing] = useState(false);

  async function submit(form: RegisterForm) {
    setProcessing(true);
    setErrors({});
    setGeneralError("");

    try {
      const { data } = await api.post<AuthResponse>("/auth/register", form);
      localStorage.setItem("token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
      navigate("/dashboard");
    } catch (err) {
      if (axios.isAxiosError(err)) {
        const payload = err.response?.data as ApiError | undefined;
        if (err.response?.status === 422 && payload?.errors) {
          setErrors(payload.errors);
        } else {
          setGeneralError(
            payload?.message ?? "Registration failed. Please try again.",
          );
        }
      }
    } finally {
      setProcessing(false);
    }
  }

  return { submit, errors, generalError, processing };
}

export function useLogout() {
  const navigate = useNavigate();
  const [processing, setProcessing] = useState(false);

  async function logout() {
    setProcessing(true);
    try {
      await api.delete("/auth/logout");
    } catch {
      // always clear local session even if the API call fails
    } finally {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      setProcessing(false);
      navigate("/login");
    }
  }

  return { logout, processing };
}
