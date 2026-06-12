import api from "../lib/axios";
import type {
  Property,
  CreatePropertyForm,
  UpdatePropertyForm,
} from "../types/property";

interface Paginated<T> {
  data: T[];
}

export async function getProperties(): Promise<Property[]> {
  const { data } = await api.get<Paginated<Property>>("/properties");
  return data.data;
}

export async function createProperty(
  form: CreatePropertyForm,
): Promise<Property> {
  const { data } = await api.post<Property>("/properties", form);
  return data;
}

export async function updateProperty(
  id: number,
  form: UpdatePropertyForm,
): Promise<Property> {
  const { data } = await api.put<Property>(`/properties/${id}`, form);
  return data;
}

export async function deleteProperty(id: number): Promise<void> {
  await api.delete(`/properties/${id}`);
}
