"use server"

import { supabase } from "@/lib/supabase"

// Mendapatkan semua topik forum
export async function getAllTopics() {
  const { data, error } = await supabase
    .from("forum_topics")
    .select("*, forum_replies(count)")
    .order("created_at", { ascending: false })

  if (error) {
    console.error("Error fetching forum topics:", error)
    return []
  }

  return data.map((topic) => ({
    ...topic,
    replies_count: topic.forum_replies[0].count,
  }))
}

// Mendapatkan topik forum berdasarkan ID
export async function getTopicById(id) {
  const { data, error } = await supabase.from("forum_topics").select("*").eq("id", id).single()

  if (error) {
    console.error(`Error fetching forum topic with id ${id}:`, error)
    return null
  }

  return data
}

// Mendapatkan balasan untuk topik forum
export async function getRepliesByTopicId(topicId) {
  const { data, error } = await supabase
    .from("forum_replies")
    .select("*, profiles(username, avatar_url)")
    .eq("topic_id", topicId)
    .order("created_at")

  if (error) {
    console.error(`Error fetching replies for topic ${topicId}:`, error)
    return []
  }

  return data
}

// Membuat topik forum baru
export async function createForumTopic(title, content, userId, category) {
  const { data, error } = await supabase
    .from("forum_topics")
    .insert([
      {
        title,
        content,
        user_id: userId,
        category,
      },
    ])
    .select()

  if (error) {
    console.error("Error creating forum topic:", error)
    return null
  }

  return data[0]
}

// Menambahkan balasan ke topik forum
export async function addReplyToTopic(topicId, content, userId) {
  const { data, error } = await supabase
    .from("forum_replies")
    .insert([
      {
        topic_id: topicId,
        content,
        user_id: userId,
      },
    ])
    .select()

  if (error) {
    console.error("Error adding reply to topic:", error)
    return null
  }

  return data[0]
}
