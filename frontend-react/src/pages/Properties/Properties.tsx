import { useEffect, useState } from "react";
import {
  getProperties,
  createProperty,
  updateProperty,
  deleteProperty,
} from "../../services/propertiesApi";
import type { Property, CreatePropertyForm } from "../../types/property";
import PropertyForm from "./PropertyForm";
import AmenityManager from "./AmenityManager";

type Modal = { kind: "create" } | { kind: "edit"; property: Property } | null;

export default function Properties() {
  const [properties, setProperties] = useState<Property[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [modal, setModal] = useState<Modal>(null);
  const [expanded, setExpanded] = useState<Set<number>>(new Set());
  const [toast, setToast] = useState("");
  const [deleting, setDeleting] = useState<number | null>(null);

  useEffect(() => {
    getProperties()
      .then(setProperties)
      .catch(() => setError("Failed to load properties."))
      .finally(() => setLoading(false));
  }, []);

  function showToast(msg: string) {
    setToast(msg);
    setTimeout(() => setToast(""), 3000);
  }

  function toggleExpanded(id: number) {
    setExpanded((prev) => {
      const next = new Set(prev);
      if (next.has(id)) {
        next.delete(id);
      } else {
        next.add(id);
      }
      return next;
    });
  }

  async function handleCreate(form: CreatePropertyForm) {
    const created = await createProperty(form);
    setProperties((prev) => [created, ...prev]);
    setModal(null);
    showToast("Property created.");
  }

  async function handleUpdate(id: number, form: CreatePropertyForm) {
    const updated = await updateProperty(id, form);
    setProperties((prev) => prev.map((p) => (p.id === id ? updated : p)));
    setModal(null);
    showToast("Property updated.");
  }

  async function handleDelete(id: number) {
    if (!confirm("Delete this property? This action cannot be undone.")) return;
    setDeleting(id);
    try {
      await deleteProperty(id);
      setProperties((prev) => prev.filter((p) => p.id !== id));
      showToast("Property deleted.");
    } catch {
      showToast("Failed to delete property.");
    } finally {
      setDeleting(null);
    }
  }

  function handlePropertyUpdated(updated: Property) {
    setProperties((prev) =>
      prev.map((p) => (p.id === updated.id ? updated : p)),
    );
  }

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-gray-900">Properties</h1>
          <p className="text-sm text-gray-500 mt-1">
            {properties.length} listing{properties.length !== 1 ? "s" : ""}
          </p>
        </div>
        <button
          onClick={() => setModal({ kind: "create" })}
          className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition"
        >
          + Add property
        </button>
      </div>

      {toast && (
        <div className="mb-4 rounded-lg bg-indigo-50 border border-indigo-200 px-4 py-2 text-sm text-indigo-700">
          {toast}
        </div>
      )}

      {/* Create / Edit modal */}
      {modal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
          <div className="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <h2 className="text-base font-semibold text-gray-900 mb-4">
              {modal.kind === "create" ? "Add Property" : "Edit Property"}
            </h2>
            <PropertyForm
              initial={modal.kind === "edit" ? modal.property : undefined}
              onSubmit={
                modal.kind === "create"
                  ? handleCreate
                  : (form) => handleUpdate(modal.property.id, form)
              }
              onCancel={() => setModal(null)}
              submitLabel={modal.kind === "create" ? "Create" : "Save changes"}
            />
          </div>
        </div>
      )}

      {loading ? (
        <p className="text-sm text-gray-500">Loading…</p>
      ) : error ? (
        <p className="text-sm text-red-600">{error}</p>
      ) : properties.length === 0 ? (
        <div className="rounded-xl border border-dashed border-gray-300 p-12 text-center">
          <p className="text-sm text-gray-500">No properties yet.</p>
          <button
            onClick={() => setModal({ kind: "create" })}
            className="mt-3 text-sm text-indigo-600 hover:underline"
          >
            Add your first property
          </button>
        </div>
      ) : (
        <div className="space-y-3">
          {properties.map((property) => (
            <div
              key={property.id}
              className="rounded-xl border border-gray-200 bg-white p-4"
            >
              <div className="flex items-start justify-between gap-4">
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2 flex-wrap">
                    <p className="font-medium text-gray-900 truncate">
                      {property.title}
                    </p>
                    <span className="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 capitalize">
                      {property.propertyType}
                    </span>
                    <span className="rounded-full bg-indigo-50 px-2 py-0.5 text-xs text-indigo-700">
                      {property.status.replace(/_/g, " ")}
                    </span>
                  </div>
                  <p className="text-sm text-gray-500 mt-0.5">
                    {property.city}, {property.state} ·{" "}
                    <span className="font-medium text-gray-800">
                      ${Number(property.price).toLocaleString()}
                    </span>
                  </p>
                  {(property.bedrooms != null ||
                    property.bathrooms != null) && (
                    <p className="text-xs text-gray-400 mt-0.5">
                      {property.bedrooms != null && `${property.bedrooms} bed`}
                      {property.bedrooms != null &&
                        property.bathrooms != null &&
                        " · "}
                      {property.bathrooms != null &&
                        `${property.bathrooms} bath`}
                    </p>
                  )}
                </div>

                <div className="flex items-center gap-2 shrink-0">
                  <button
                    onClick={() => toggleExpanded(property.id)}
                    className="rounded border border-gray-200 px-2.5 py-1 text-xs text-gray-600 hover:bg-gray-50 transition"
                  >
                    {expanded.has(property.id) ? "Hide amenities" : "Amenities"}
                  </button>
                  <button
                    onClick={() => setModal({ kind: "edit", property })}
                    className="rounded border border-gray-300 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 transition"
                  >
                    Edit
                  </button>
                  <button
                    disabled={deleting === property.id}
                    onClick={() => handleDelete(property.id)}
                    className="rounded border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50 disabled:opacity-50 transition"
                  >
                    Delete
                  </button>
                </div>
              </div>

              {expanded.has(property.id) && (
                <AmenityManager
                  property={property}
                  onUpdated={handlePropertyUpdated}
                />
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
