"use server"

import { supabase } from "@/lib/supabase"

// Mendapatkan semua artikel
export async function getAllArticles() {
  const { data, error } = await supabase
    .from("articles")
    .select("*, profiles:author_id(username, full_name)")
    .eq("status", "approved")
    .order("created_at", { ascending: false })

  if (error) {
    console.error("Error fetching articles:", error)
    return { error: error.message }
  }

  return { articles: data }
}

// Mendapatkan artikel berdasarkan ID
export async function getArticleById(id) {
  const { data, error } = await supabase
    .from("articles")
    .select("*, profiles:author_id(username, full_name, role)")
    .eq("id", id)
    .eq("status", "approved")
    .single()

  if (error) {
    console.error(`Error fetching article with id ${id}:`, error)
    return { error: error.message }
  }

  return { article: data }
}

// Mencari artikel berdasarkan kata kunci
export async function searchArticles(query) {
  const { data, error } = await supabase
    .from("articles")
    .select("*, profiles:author_id(username, full_name)")
    .eq("status", "approved")
    .or(`title.ilike.%${query}%, content.ilike.%${query}%, excerpt.ilike.%${query}%`)
    .order("created_at", { ascending: false })

  if (error) {
    console.error("Error searching articles:", error)
    return { error: error.message }
  }

  return { articles: data }
}

// Memfilter artikel berdasarkan kategori
export async function filterArticlesByCategory(category) {
  const { data, error } = await supabase
    .from("articles")
    .select("*, profiles:author_id(username, full_name)")
    .eq("status", "approved")
    .eq("category", category)
    .order("created_at", { ascending: false })

  if (error) {
    console.error(`Error filtering articles by category ${category}:`, error)
    return { error: error.message }
  }

  return { articles: data }
}

// Mendapatkan artikel terbaru
export async function getRecentArticles(limit = 3) {
  const { data, error } = await supabase
    .from("articles")
    .select("*")
    .eq("status", "approved")
    .order("created_at", { ascending: false })
    .limit(limit)

  if (error) {
    console.error("Error fetching recent articles:", error)
    return { error: error.message }
  }

  return { articles: data }
}

// Admin: Menyetujui artikel
export async function approveArticle(id) {
  const { data, error } = await supabase.from("articles").update({ status: "approved" }).eq("id", id).select()

  if (error) {
    console.error(`Error approving article with id ${id}:`, error)
    return { error: error.message }
  }

  return { article: data[0] }
}

// Admin: Menolak artikel
export async function rejectArticle(id, comments) {
  const { data, error } = await supabase
    .from("articles")
    .update({
      status: "rejected",
      admin_comments: comments,
    })
    .eq("id", id)
    .select()

  if (error) {
    console.error(`Error rejecting article with id ${id}:`, error)
    return { error: error.message }
  }

  return { article: data[0] }
}
