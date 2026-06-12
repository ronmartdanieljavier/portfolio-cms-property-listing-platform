import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import InputField from "../InputField";

describe("InputField", () => {
  it("renders label and input", () => {
    render(<InputField id="email" label="Email address" />);
    expect(screen.getByLabelText("Email address")).toBeInTheDocument();
  });

  it("shows error message when provided", () => {
    render(<InputField id="email" label="Email" error="Email is required." />);
    expect(screen.getByText("Email is required.")).toBeInTheDocument();
  });

  it("applies error styles when error is present", () => {
    render(<InputField id="email" label="Email" error="Invalid email." />);
    expect(screen.getByLabelText("Email")).toHaveClass("border-red-400");
  });

  it("does not show error element when no error", () => {
    render(<InputField id="email" label="Email" />);
    expect(screen.queryByRole("paragraph")).not.toBeInTheDocument();
  });

  it("disables the input when disabled prop is set", () => {
    render(<InputField id="email" label="Email" disabled />);
    expect(screen.getByLabelText("Email")).toBeDisabled();
  });

  it("forwards onChange when user types", async () => {
    const onChange = vi.fn();
    render(<InputField id="name" label="Name" onChange={onChange} />);
    await userEvent.type(screen.getByLabelText("Name"), "R");
    expect(onChange).toHaveBeenCalled();
  });
});
