import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { vi } from "vitest";
import { MemoryRouter } from "react-router-dom";
import Login from "../Login";

const mockSubmit = vi.fn();
const mockErrors = vi.fn(() => ({}));
const mockGeneralError = vi.fn(() => "");
const mockProcessing = vi.fn(() => false);

vi.mock("../../../hooks/useAuth", () => ({
  useLogin: () => ({
    submit: mockSubmit,
    errors: mockErrors(),
    generalError: mockGeneralError(),
    processing: mockProcessing(),
  }),
}));

function renderLogin() {
  return render(
    <MemoryRouter>
      <Login />
    </MemoryRouter>,
  );
}

beforeEach(() => vi.clearAllMocks());

describe("Login page", () => {
  it("renders email, password fields and submit button", () => {
    renderLogin();
    expect(screen.getByLabelText("Email address")).toBeInTheDocument();
    expect(screen.getByLabelText("Password")).toBeInTheDocument();
    expect(screen.getByRole("button", { name: "Sign in" })).toBeInTheDocument();
  });

  it("renders a link to the register page", () => {
    renderLogin();
    expect(screen.getByRole("link", { name: "Create one" })).toHaveAttribute(
      "href",
      "/register",
    );
  });

  it("calls submit with form values on submit", async () => {
    renderLogin();
    await userEvent.type(
      screen.getByLabelText("Email address"),
      "ron@example.com",
    );
    await userEvent.type(screen.getByLabelText("Password"), "password");
    await userEvent.click(screen.getByRole("button", { name: "Sign in" }));

    await waitFor(() =>
      expect(mockSubmit).toHaveBeenCalledWith({
        email: "ron@example.com",
        password: "password",
      }),
    );
  });

  it("displays a general error banner when generalError is set", () => {
    mockGeneralError.mockReturnValueOnce("Invalid credentials.");
    renderLogin();
    expect(screen.getByText("Invalid credentials.")).toBeInTheDocument();
  });

  it("displays field-level errors under the relevant inputs", () => {
    mockErrors.mockReturnValueOnce({ email: ["The email field is required."] });
    renderLogin();
    expect(
      screen.getByText("The email field is required."),
    ).toBeInTheDocument();
  });

  it("disables the button and shows loading text while processing", () => {
    mockProcessing.mockReturnValueOnce(true);
    renderLogin();
    const btn = screen.getByRole("button", { name: "Signing in…" });
    expect(btn).toBeDisabled();
  });
});
