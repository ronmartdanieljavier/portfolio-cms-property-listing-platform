import { useState } from "react";
import { Link } from "react-router-dom";
import InputField from "../../components/InputField";
import { useLogin } from "../../hooks/useAuth";
import type { LoginForm } from "../../types/auth";

export default function Login() {
  const { submit, errors, generalError, processing } = useLogin();

  const [form, setForm] = useState<LoginForm>({
    email: "",
    password: "",
  });

  function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  }

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    submit(form);
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 px-4">
      <div className="w-full max-w-md">
        <div className="bg-white rounded-2xl shadow-md p-8 space-y-6">
          <div className="text-center">
            <h1 className="text-2xl font-bold text-gray-900">Welcome back</h1>
            <p className="mt-1 text-sm text-gray-500">
              Sign in to your account
            </p>
          </div>

          {generalError && (
            <div className="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
              {generalError}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4" noValidate>
            <InputField
              id="email"
              label="Email address"
              type="email"
              name="email"
              value={form.email}
              onChange={handleChange}
              autoComplete="email"
              required
              disabled={processing}
              error={errors.email?.[0]}
            />

            <InputField
              id="password"
              label="Password"
              type="password"
              name="password"
              value={form.password}
              onChange={handleChange}
              autoComplete="current-password"
              required
              disabled={processing}
              error={errors.password?.[0]}
            />

            <button
              type="submit"
              disabled={processing}
              className="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
              {processing ? "Signing in…" : "Sign in"}
            </button>
          </form>

          <p className="text-center text-sm text-gray-500">
            Don't have an account?{" "}
            <Link
              to="/register"
              className="font-medium text-indigo-600 hover:text-indigo-500"
            >
              Create one
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}
