"use server"

import { supabase } from "@/lib/supabase"
import { getCurrentUser } from "./auth"

// Mendapatkan profil pengguna
export async function getUserProfile() {
  const user = await getCurrentUser()

  if (!user) {
    return { error: "Tidak ada pengguna yang login" }
  }

  const { data, error } = await supabase.from("profiles").select("*").eq("id", user.id).single()

  if (error) {
    console.error("Error fetching profile:", error)
    return { error: error.message }
  }

  return { profile: data }
}

// Memperbarui profil pengguna
export async function updateUserProfile(profileData) {
  const user = await getCurrentUser()

  if (!user) {
    return { error: "Tidak ada pengguna yang login" }
  }

  const { data, error } = await supabase.from("profiles").update(profileData).eq("id", user.id).select()

  if (error) {
    console.error("Error updating profile:", error)
    return { error: error.message }
  }

  return { profile: data[0] }
}

// Mengunggah avatar pengguna
export async function uploadAvatar(avatarFile) {
  const user = await getCurrentUser()

  if (!user) {
    return { error: "Tidak ada pengguna yang login" }
  }

  // Generate a unique file name
  const fileName = `avatar-${user.id}-${Date.now()}`

  // Upload the file to Supabase Storage
  const { data: uploadData, error: uploadError } = await supabase.storage.from("avatars").upload(fileName, avatarFile, {
    cacheControl: "3600",
    upsert: true,
  })

  if (uploadError) {
    console.error("Error uploading avatar:", uploadError)
    return { error: uploadError.message }
  }

  // Get the public URL
  const {
    data: { publicUrl },
  } = supabase.storage.from("avatars").getPublicUrl(fileName)

  // Update the user profile with the new avatar URL
  const { data, error } = await supabase.from("profiles").update({ avatar_url: publicUrl }).eq("id", user.id).select()

  if (error) {
    console.error("Error updating profile with avatar:", error)
    return { error: error.message }
  }

  return { profile: data[0] }
}

// Mendapatkan profil berdasarkan role
export async function getUsersByRole(role) {
  const { data, error } = await supabase.from("profiles").select("*").eq("role", role)

  if (error) {
    console.error(`Error fetching users with role ${role}:`, error)
    return { error: error.message }
  }

  return { users: data }
}

// Admin: Mengubah role pengguna (hanya untuk admin)
export async function updateUserRole(userId, newRole) {
  const currentUser = await getCurrentUser()

  // Check if current user is admin
  const { data: adminCheck } = await supabase.from("profiles").select("role").eq("id", currentUser.id).single()

  if (!adminCheck || adminCheck.role !== "admin") {
    return { error: "Tidak memiliki izin untuk mengubah role pengguna" }
  }

  const { data, error } = await supabase.from("profiles").update({ role: newRole }).eq("id", userId).select()

  if (error) {
    console.error("Error updating user role:", error)
    return { error: error.message }
  }

  return { success: true, user: data[0] }
}
