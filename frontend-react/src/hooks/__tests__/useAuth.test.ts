import { renderHook, act } from "@testing-library/react";
import { vi, beforeEach, afterEach } from "vitest";
import axios from "axios";

// vi.hoisted ensures mockApi is initialised before vi.mock hoisting runs
const { mockApi } = vi.hoisted(() => ({
  mockApi: { post: vi.fn(), delete: vi.fn() },
}));

vi.mock("../../lib/axios", () => ({ default: mockApi }));

const mockNavigate = vi.fn();
vi.mock("react-router-dom", () => ({ useNavigate: () => mockNavigate }));

import { useLogin, useRegister, useLogout } from "../useAuth";

const mockUser = {
  id: 1,
  name: "Ron",
  email: "ron@example.com",
  role: "agent" as const,
  is_active: true,
  created_at: "",
  updated_at: "",
};

beforeEach(() => {
  localStorage.clear();
  vi.clearAllMocks();
});

afterEach(() => {
  localStorage.clear();
});

// ─── useLogin ────────────────────────────────────────────────────────────────

describe("useLogin", () => {
  it("stores token and user then navigates to /dashboard on success", async () => {
    mockApi.post.mockResolvedValueOnce({
      data: { user: mockUser, token: "tok123" },
    });

    const { result } = renderHook(() => useLogin());
    await act(() =>
      result.current.submit({ email: "ron@example.com", password: "password" }),
    );

    expect(localStorage.getItem("token")).toBe("tok123");
    expect(JSON.parse(localStorage.getItem("user")!)).toEqual(mockUser);
    expect(mockNavigate).toHaveBeenCalledWith("/dashboard");
  });

  it("sets generalError on non-422 API failure", async () => {
    const err = Object.assign(new Error(), {
      isAxiosError: true,
      response: { status: 401, data: { message: "Invalid credentials." } },
    });
    mockApi.post.mockRejectedValueOnce(err);
    vi.spyOn(axios, "isAxiosError").mockReturnValueOnce(true);

    const { result } = renderHook(() => useLogin());
    await act(() =>
      result.current.submit({ email: "x@x.com", password: "wrong" }),
    );

    expect(result.current.generalError).toBe("Invalid credentials.");
    expect(mockNavigate).not.toHaveBeenCalled();
  });

  it("sets field errors on 422 validation response", async () => {
    const err = Object.assign(new Error(), {
      isAxiosError: true,
      response: {
        status: 422,
        data: { errors: { email: ["The email field is required."] } },
      },
    });
    mockApi.post.mockRejectedValueOnce(err);
    vi.spyOn(axios, "isAxiosError").mockReturnValueOnce(true);

    const { result } = renderHook(() => useLogin());
    await act(() => result.current.submit({ email: "", password: "" }));

    expect(result.current.errors.email?.[0]).toBe(
      "The email field is required.",
    );
  });

  it("sets processing true during request and false after", async () => {
    let resolve!: (v: unknown) => void;
    mockApi.post.mockReturnValueOnce(new Promise((r) => (resolve = r)));

    const { result } = renderHook(() => useLogin());

    act(() => {
      result.current.submit({ email: "a@b.com", password: "pass" });
    });
    expect(result.current.processing).toBe(true);

    await act(() => {
      resolve({ data: { user: mockUser, token: "t" } });
    });
    expect(result.current.processing).toBe(false);
  });
});

// ─── useRegister ─────────────────────────────────────────────────────────────

describe("useRegister", () => {
  it("stores token and user then navigates to /dashboard on success", async () => {
    mockApi.post.mockResolvedValueOnce({
      data: { user: mockUser, token: "tok456" },
    });

    const { result } = renderHook(() => useRegister());
    await act(() =>
      result.current.submit({
        name: "Ron",
        email: "ron@example.com",
        password: "password",
        password_confirmation: "password",
      }),
    );

    expect(localStorage.getItem("token")).toBe("tok456");
    expect(mockNavigate).toHaveBeenCalledWith("/dashboard");
  });

  it("sets field errors on 422 response", async () => {
    const err = Object.assign(new Error(), {
      isAxiosError: true,
      response: {
        status: 422,
        data: { errors: { email: ["The email has already been taken."] } },
      },
    });
    mockApi.post.mockRejectedValueOnce(err);
    vi.spyOn(axios, "isAxiosError").mockReturnValueOnce(true);

    const { result } = renderHook(() => useRegister());
    await act(() =>
      result.current.submit({
        name: "Ron",
        email: "taken@example.com",
        password: "password",
        password_confirmation: "password",
      }),
    );

    expect(result.current.errors.email?.[0]).toBe(
      "The email has already been taken.",
    );
  });
});

// ─── useLogout ────────────────────────────────────────────────────────────────

describe("useLogout", () => {
  it("clears localStorage and navigates to /login on success", async () => {
    localStorage.setItem("token", "tok");
    localStorage.setItem("user", JSON.stringify(mockUser));
    mockApi.delete.mockResolvedValueOnce({});

    const { result } = renderHook(() => useLogout());
    await act(() => result.current.logout());

    expect(localStorage.getItem("token")).toBeNull();
    expect(localStorage.getItem("user")).toBeNull();
    expect(mockNavigate).toHaveBeenCalledWith("/login");
  });

  it("still clears localStorage and navigates when API call fails", async () => {
    localStorage.setItem("token", "tok");
    mockApi.delete.mockRejectedValueOnce(new Error("Network error"));

    const { result } = renderHook(() => useLogout());
    await act(() => result.current.logout());

    expect(localStorage.getItem("token")).toBeNull();
    expect(mockNavigate).toHaveBeenCalledWith("/login");
  });
});
