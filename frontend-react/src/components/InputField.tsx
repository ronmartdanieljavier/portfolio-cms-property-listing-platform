import type { InputHTMLAttributes } from "react";

interface Props extends InputHTMLAttributes<HTMLInputElement> {
  label: string;
  error?: string;
}

export default function InputField({ label, error, id, ...props }: Props) {
  return (
    <div>
      <label
        htmlFor={id}
        className="block text-sm font-medium text-gray-700 mb-1"
      >
        {label}
      </label>
      <input
        id={id}
        {...props}
        className={[
          "w-full rounded-lg border px-4 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition",
          "focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500",
          error ? "border-red-400 bg-red-50" : "border-gray-300 bg-white",
          props.disabled ? "opacity-50 cursor-not-allowed" : "",
        ].join(" ")}
      />
      {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
    </div>
  );
}
