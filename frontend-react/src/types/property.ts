export type PropertyStatus = "for_sale" | "for_rent" | "sold" | "rented";
export type PropertyType =
  | "house"
  | "apartment"
  | "condo"
  | "townhouse"
  | "land"
  | "commercial";

export interface Amenity {
  id: number;
  name: string;
  createdAt: string;
  updatedAt: string;
}

export interface Property {
  id: number;
  agentId: number;
  title: string;
  description: string | null;
  price: string;
  propertyType: PropertyType;
  status: PropertyStatus;
  bedrooms: number | null;
  bathrooms: number | null;
  floorArea: string | null;
  lotArea: string | null;
  floors: number | null;
  address: string;
  city: string;
  state: string;
  country: string;
  postcode: string | null;
  latitude: string | null;
  longitude: string | null;
  amenities: Amenity[] | null;
  createdAt: string;
  updatedAt: string;
}

export interface CreatePropertyForm {
  title: string;
  description?: string;
  price: number | string;
  propertyType: PropertyType;
  status?: PropertyStatus;
  bedrooms?: number;
  bathrooms?: number;
  floorArea?: number;
  lotArea?: number;
  floors?: number;
  address: string;
  city: string;
  state: string;
  country?: string;
  postcode?: string;
  latitude?: number;
  longitude?: number;
}

export type UpdatePropertyForm = Partial<CreatePropertyForm>;
