import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { vi } from "vitest";
import { MemoryRouter } from "react-router-dom";
import Dashboard from "../Dashboard";

const mockLogout = vi.fn();
const mockProcessing = vi.fn(() => false);

vi.mock("../../hooks/useAuth", () => ({
  useLogout: () => ({
    logout: mockLogout,
    processing: mockProcessing(),
  }),
}));

const mockUser = {
  id: 1,
  name: "Ron",
  email: "ron@example.com",
  role: "agent",
  is_active: true,
  created_at: "",
  updated_at: "",
};

function renderDashboard() {
  return render(
    <MemoryRouter>
      <Dashboard />
    </MemoryRouter>,
  );
}

beforeEach(() => {
  vi.clearAllMocks();
  localStorage.clear();
});

describe("Dashboard page", () => {
  it("renders the sign out button", () => {
    renderDashboard();
    expect(
      screen.getByRole("button", { name: "Sign out" }),
    ).toBeInTheDocument();
  });

  it("shows the user name and role from localStorage", () => {
    localStorage.setItem("user", JSON.stringify(mockUser));
    renderDashboard();
    // header <p> is the only standalone "Ron" text node
    expect(screen.getAllByText("Ron").length).toBeGreaterThanOrEqual(1);
    expect(screen.getByText("agent")).toBeInTheDocument();
  });

  it("shows welcome message with user name", () => {
    localStorage.setItem("user", JSON.stringify(mockUser));
    renderDashboard();
    // "Ron." text is split across elements — query the containing paragraph
    expect(screen.getByText(/Welcome back,/)).toBeInTheDocument();
    expect(screen.getByText(/Ron/, { selector: "span" })).toBeInTheDocument();
  });

  it("calls logout when sign out button is clicked", async () => {
    renderDashboard();
    await userEvent.click(screen.getByRole("button", { name: "Sign out" }));
    await waitFor(() => expect(mockLogout).toHaveBeenCalledOnce());
  });

  it("disables button and shows loading text while processing", () => {
    mockProcessing.mockReturnValueOnce(true);
    renderDashboard();
    const btn = screen.getByRole("button", { name: "Signing out…" });
    expect(btn).toBeDisabled();
  });
});
