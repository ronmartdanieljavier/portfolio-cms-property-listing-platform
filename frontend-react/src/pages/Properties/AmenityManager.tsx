import { useEffect, useState } from "react";
import {
  getAllAmenities,
  addAmenity,
  updateAmenities,
  deleteAmenity,
} from "../../services/amenitiesApi";
import type { Amenity, Property } from "../../types/property";

interface Props {
  property: Property;
  onUpdated: (updated: Property) => void;
  readOnly?: boolean;
}

export default function AmenityManager({
  property,
  onUpdated,
  readOnly = false,
}: Props) {
  const [allAmenities, setAllAmenities] = useState<Amenity[]>([]);
  const [processing, setProcessing] = useState(false);
  const [toast, setToast] = useState("");
  const [selectedId, setSelectedId] = useState<number | "">("");

  const currentIds = new Set((property.amenities ?? []).map((a) => a.id));

  useEffect(() => {
    getAllAmenities()
      .then(setAllAmenities)
      .catch(() => {});
  }, []);

  function showToast(msg: string) {
    setToast(msg);
    setTimeout(() => setToast(""), 3000);
  }

  async function handleAdd() {
    if (!selectedId) return;
    setProcessing(true);
    try {
      const amenities = await addAmenity(property.id, [selectedId as number]);
      onUpdated({ ...property, amenities });
      setSelectedId("");
      showToast("Amenity added.");
    } catch {
      showToast("Failed to add amenity.");
    } finally {
      setProcessing(false);
    }
  }

  async function handleSync(amenityIds: number[]) {
    setProcessing(true);
    try {
      const amenities = await updateAmenities(property.id, amenityIds);
      onUpdated({ ...property, amenities });
      showToast("Amenities updated.");
    } catch {
      showToast("Failed to update amenities.");
    } finally {
      setProcessing(false);
    }
  }

  async function handleDelete(amenityId: number) {
    if (!confirm("Remove this amenity from the property?")) return;
    setProcessing(true);
    try {
      await deleteAmenity(property.id, amenityId);
      onUpdated({
        ...property,
        amenities: (property.amenities ?? []).filter((a) => a.id !== amenityId),
      });
      showToast("Amenity removed.");
    } catch {
      showToast("Failed to remove amenity.");
    } finally {
      setProcessing(false);
    }
  }

  const availableToAdd = allAmenities.filter((a) => !currentIds.has(a.id));

  return (
    <div className="mt-3 border-t border-gray-100 pt-3">
      {toast && (
        <p className="mb-2 text-xs text-indigo-700 bg-indigo-50 rounded px-2 py-1">
          {toast}
        </p>
      )}

      <p className="text-xs font-medium text-gray-500 mb-2">Amenities</p>

      <div className="flex flex-wrap gap-1.5 mb-3">
        {(property.amenities ?? []).length === 0 ? (
          <p className="text-xs text-gray-400">None</p>
        ) : (
          (property.amenities ?? []).map((amenity) => (
            <span
              key={amenity.id}
              className="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs text-gray-700"
            >
              {amenity.name}
              {!readOnly && (
                <button
                  disabled={processing}
                  onClick={() => handleDelete(amenity.id)}
                  className="text-gray-400 hover:text-red-500 disabled:opacity-50 leading-none"
                  title="Remove"
                >
                  ×
                </button>
              )}
            </span>
          ))
        )}
      </div>

      {!readOnly && availableToAdd.length > 0 && (
        <div className="flex gap-2">
          <select
            value={selectedId}
            onChange={(e) =>
              setSelectedId(e.target.value ? Number(e.target.value) : "")
            }
            className="flex-1 rounded border border-gray-300 px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500"
          >
            <option value="">Select amenity…</option>
            {availableToAdd.map((a) => (
              <option key={a.id} value={a.id}>
                {a.name}
              </option>
            ))}
          </select>
          <button
            disabled={!selectedId || processing}
            onClick={handleAdd}
            className="rounded border border-indigo-300 bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 disabled:opacity-50 transition"
          >
            Add
          </button>
          {(property.amenities ?? []).length > 0 && (
            <button
              disabled={processing}
              onClick={() =>
                handleSync((property.amenities ?? []).map((a) => a.id))
              }
              className="rounded border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 transition"
            >
              Sync
            </button>
          )}
        </div>
      )}
    </div>
  );
}
