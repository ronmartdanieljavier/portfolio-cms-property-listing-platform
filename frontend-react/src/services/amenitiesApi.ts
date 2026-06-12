import api from "../lib/axios";
import type { Amenity } from "../types/property";

export async function getAllAmenities(): Promise<Amenity[]> {
  const { data } = await api.get<Amenity[]>("/amenities");
  return data;
}

export async function addAmenity(
  propertyId: number,
  amenityIds: number[],
): Promise<Amenity[]> {
  const { data } = await api.post<Amenity[]>(
    `/properties/${propertyId}/amenities`,
    { amenityIds },
  );
  return data;
}

export async function updateAmenities(
  propertyId: number,
  amenityIds: number[],
): Promise<Amenity[]> {
  const { data } = await api.put<Amenity[]>(
    `/properties/${propertyId}/amenities`,
    { amenityIds },
  );
  return data;
}

export async function deleteAmenity(
  propertyId: number,
  amenityId: number,
): Promise<void> {
  await api.delete(`/properties/${propertyId}/amenities/${amenityId}`);
}
