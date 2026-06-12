import { vi } from "vitest";

export const mockApi = {
  post: vi.fn(),
  delete: vi.fn(),
};

vi.mock("../../lib/axios", () => ({ default: mockApi }));
