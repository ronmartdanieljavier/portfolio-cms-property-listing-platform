import { render, screen } from "@testing-library/react";
import { MemoryRouter } from "react-router-dom";
import Dashboard from "../Dashboard";

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
  localStorage.clear();
});

describe("Dashboard page", () => {
  it("shows welcome message without a user name when not logged in", () => {
    renderDashboard();
    expect(screen.getByText(/Welcome back,/)).toBeInTheDocument();
    expect(screen.getByText("user", { selector: "span" })).toBeInTheDocument();
  });

  it("shows welcome message with user name from localStorage", () => {
    localStorage.setItem("user", JSON.stringify(mockUser));
    renderDashboard();
    expect(screen.getByText(/Welcome back,/)).toBeInTheDocument();
    expect(screen.getByText("Ron", { selector: "span" })).toBeInTheDocument();
  });
});
