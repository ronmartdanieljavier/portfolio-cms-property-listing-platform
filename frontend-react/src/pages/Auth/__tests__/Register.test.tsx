import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { vi } from "vitest";
import { MemoryRouter } from "react-router-dom";
import Register from "../Register";

const mockSubmit = vi.fn();
const mockErrors = vi.fn(() => ({}));
const mockGeneralError = vi.fn(() => "");
const mockProcessing = vi.fn(() => false);

vi.mock("../../../hooks/useAuth", () => ({
  useRegister: () => ({
    submit: mockSubmit,
    errors: mockErrors(),
    generalError: mockGeneralError(),
    processing: mockProcessing(),
  }),
}));

function renderRegister() {
  return render(
    <MemoryRouter>
      <Register />
    </MemoryRouter>,
  );
}

beforeEach(() => vi.clearAllMocks());

describe("Register page", () => {
  it("renders all four fields and submit button", () => {
    renderRegister();
    expect(screen.getByLabelText("Full name")).toBeInTheDocument();
    expect(screen.getByLabelText("Email address")).toBeInTheDocument();
    expect(screen.getByLabelText("Password")).toBeInTheDocument();
    expect(screen.getByLabelText("Confirm password")).toBeInTheDocument();
    expect(
      screen.getByRole("button", { name: "Create account" }),
    ).toBeInTheDocument();
  });

  it("renders a link to the login page", () => {
    renderRegister();
    expect(screen.getByRole("link", { name: "Sign in" })).toHaveAttribute(
      "href",
      "/login",
    );
  });

  it("calls submit with all form values on submit", async () => {
    renderRegister();
    await userEvent.type(screen.getByLabelText("Full name"), "Ron");
    await userEvent.type(
      screen.getByLabelText("Email address"),
      "ron@example.com",
    );
    await userEvent.type(screen.getByLabelText("Password"), "password");
    await userEvent.type(screen.getByLabelText("Confirm password"), "password");
    await userEvent.click(
      screen.getByRole("button", { name: "Create account" }),
    );

    await waitFor(() =>
      expect(mockSubmit).toHaveBeenCalledWith({
        name: "Ron",
        email: "ron@example.com",
        password: "password",
        password_confirmation: "password",
      }),
    );
  });

  it("displays a general error banner when generalError is set", () => {
    mockGeneralError.mockReturnValueOnce(
      "Registration failed. Please try again.",
    );
    renderRegister();
    expect(
      screen.getByText("Registration failed. Please try again."),
    ).toBeInTheDocument();
  });

  it("displays field errors under the relevant inputs", () => {
    mockErrors.mockReturnValueOnce({
      email: ["The email has already been taken."],
    });
    renderRegister();
    expect(
      screen.getByText("The email has already been taken."),
    ).toBeInTheDocument();
  });

  it("disables the button and shows loading text while processing", () => {
    mockProcessing.mockReturnValueOnce(true);
    renderRegister();
    const btn = screen.getByRole("button", { name: "Creating account…" });
    expect(btn).toBeDisabled();
  });
});
