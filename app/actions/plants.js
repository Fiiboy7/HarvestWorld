"use server"

import { supabase } from "@/lib/supabase"

// Mendapatkan semua tanaman
export async function getAllPlants() {
  const { data, error } = await supabase.from("plants").select("*").order("name")

  if (error) {
    console.error("Error fetching plants:", error)
    return []
  }

  return data
}

// Mendapatkan tanaman berdasarkan ID
export async function getPlantById(id) {
  const { data, error } = await supabase.from("plants").select("*").eq("id", id).single()

  if (error) {
    console.error(`Error fetching plant with id ${id}:`, error)
    return null
  }

  return data
}

// Mencari tanaman berdasarkan kata kunci
export async function searchPlants(query) {
  const { data, error } = await supabase.from("plants").select("*").ilike("name", `%${query}%`).order("name")

  if (error) {
    console.error("Error searching plants:", error)
    return []
  }

  return data
}

// Memfilter tanaman berdasarkan kategori
export async function filterPlantsByCategory(category) {
  const { data, error } = await supabase.from("plants").select("*").eq("category", category).order("name")

  if (error) {
    console.error(`Error filtering plants by category ${category}:`, error)
    return []
  }

  return data
}
